<?php
include_once('common_functions.php');

class user
{
	private $link;
	public $fname;
	public $lname;
	public $uid;
	public $orgID;
	public $teamID;
	public $courses = array();
	
	/* function setUID($u)
	{
		$this->uid = $u;
	} */
	
	function __construct($u)
	{
		$this->link  = connect_db_read();
		$this->uid = $u;
		if($stmt = mysqli_prepare($this->link, "SELECT first_name, last_name, org FROM `users` WHERE id = ? LIMIT 1") or die(mysqli_error($this->link)))
		{
			mysqli_stmt_bind_param($stmt, "i", $u);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $this->fname, $this->lname, $this->orgID);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
		}
		
		if($stmt = "SELECT id FROM `classes` WHERE archived = 0 AND student = ".$u)
		{
			if(mysqli_multi_query($this->link, $stmt))
			{
				if($result = mysqli_store_result($this->link))
				{
					while($row = mysqli_fetch_assoc($result))
						$this->courses[] = new course($row["id"], $this->link);
					mysqli_free_result($result);
				}else
					echo 'Failure '.mysqli_error($this->link);
			}
		}
	}
}

class course
{
	public $title;
	public $instructor;
	public $earnedPoints = 0;
	public $totalPoints = 0;
	public $inactivePoints = 0;
	public $credits;
	public $cn;
	public $currentGrade; // stored as float
	public $categories = array();
	
	
	function __construct($c, &$link)
	{
		if($stmt = mysqli_prepare($link, "SELECT name, instructor, total_pts, creditHours, catelogNum FROM `classes` WHERE id = ? LIMIT 1") or die(mysqli_error($this->link)))
		{
			mysqli_stmt_bind_param($stmt, "i", $c);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $this->title, $this->instructor, $this->totalPoints, $this->credits, $this->cn);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
		}
		if($stmt = "SELECT id FROM `grade_categories` WHERE class = ".$c)
		{
			if(mysqli_multi_query($link, $stmt))
			{
				if($result = mysqli_store_result($link))
				{
					while($row = mysqli_fetch_assoc($result))
						$this->categories[] = new category($row["id"], $link);
					mysqli_free_result($result);
				}else
					echo 'Failure '.mysqli_error($this->link);
			}
		}
		foreach($this->categories as $cat)
		{
			if(count($cat->assignments) == 0)
			{
				$this->inactivePoints += $cat->maxPoints;
			}
			$this->earnedPoints += $cat->catPoints();
			//echo $cat->catPoints().'<br>';
			
		}
		$this->currentGrade = (float)($this->earnedPoints/($this->totalPoints-$this->inactivePoints));
		//echo ($coursePoints).'<br>';
	}
}

class category
{
	public $maxPoints = 0;
	public $totalEarned = 0;
	public $totalOffered = 0;
	public $lowestDrop = 0;
	public $dropAfter = 0;
	public $finalReplace = 0;
	public $title;
	public $assignments = array();
	
	public function catPoints()
	{
		return $this->totalOffered == 0 ? 0: (($this->totalEarned/$this->totalOffered)*$this->maxPoints);
	}
	
	// find the lowest percentage and return the num and denom values in an array
	private function lowestGrade()
	{
		$result = array('num'=>NULL, 'den'=>NULL); // this is what is returned
		// initialize temp variables
		/* $gradeObj = reset($this->assignments);*/
		
		foreach($this->assignments as $as)
		{
			if($as->dropped == false)
			{
				if(!isset($val))
				{
					$val = $as->percent();
					$lowest = $as;
					$result['num'] = $as->earned;
					$result['den'] = $as->denom;
				}else{
					if($as->percent() < $val)
					{
						$val = $as->percent();
						$lowest = $as;
						$result['num'] = $as->earned;
						$result['den'] = $as->denom;
					}
				}
			}
		}
		$lowest->drop();
		return $result;
		
		/*print_r($gradeObj->dropped);
		while($gradeObj->dropped == true)
		{
			$gradeObj = next($this->assignments);
		} */
	}
	
	function __construct($d, $link)
	{
		if($stmt = mysqli_prepare($link, "SELECT name, max_points, lowest_drop, drop_after, finalReplacesLowExam FROM `grade_categories` WHERE id = ? LIMIT 1") or die(mysqli_error($this->link)))
		{
			mysqli_stmt_bind_param($stmt, "i", $d);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $this->title, $this->maxPoints, $this->lowestDrop, $this->dropAfter, $this->finalReplace);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
		}
		if($stmt = "SELECT id FROM `grades` WHERE category = ".$d)
		{
			if(mysqli_multi_query($link, $stmt))
			{
				if($result = mysqli_store_result($link))
				{
					while($row = mysqli_fetch_assoc($result))
						$this->assignments[] = new assignment($row["id"], $link);
					mysqli_free_result($result);
					foreach($this->assignments as $grade)
					{
						$this->totalEarned += $grade->earned;
						$this->totalOffered += $grade->denom;
						// Look for dropped grades
					}
					if($this->lowestDrop > 0 || $this->dropAfter > 0)
					{
						// determine HOW MANY assignments need to be dropped
						$dropped = ($this->dropAfter > 0) ? (count($this->assignments)-$this->dropAfter) : $this->lowestDrop;
						
						// now find the lowest grades and mark them as dropped. Remove their numbers from the totals
						for($i = 0; $i < $dropped; $i++)
						{
							$low = $this->lowestGrade();
							$this->totalEarned -= $low['num'];
							$this->totalOffered -= $low['den'];
						}
					}
				}else
					echo 'Failure '.mysqli_error($this->link);
			}
		}
	}
}

class assignment
{
	public $description;
	public $earned = 0;
	public $denom = 0;
	public $dropped = false;
	
	public function drop()
	{
		$this->dropped = true;
	}
	
	public function percent()
	{
		return (float)($this->earned/$this->denom);
	}
	
	function __construct($g, $link)
	{
		if($stmt = mysqli_prepare($link, "SELECT description, points_earned, max_points FROM `grades` WHERE id = ? LIMIT 1") or die(mysqli_error($this->link)))
		{
			mysqli_stmt_bind_param($stmt, "i", $g);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $this->description, $this->earned, $this->denom);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
		}
	}
}
?>