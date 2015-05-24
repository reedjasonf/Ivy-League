<?php
include_once('common_functions.php');

function print_summary_all_classes($uid) {
	$link = connect_db_read();
	if($class_stmt = mysqli_prepare($link, "SELECT id, name, total_pts FROM `classes` WHERE student = ? AND archived = 0"))
	{
		mysqli_stmt_bind_param($class_stmt, "i", $uid);
		mysqli_stmt_execute($class_stmt);
		mysqli_stmt_bind_result($class_stmt, $class_id, $class_name, $class_total_pts);
	}
	
	while(mysqli_stmt_fetch($class_stmt))
	{
		// for each class in the database print it's name (already obtained) and the current points over total
		// to get the total points we need to get the categories and add the max_points together
		$link2 = connect_db_read();
		$class_max_points = 0;
		$total_points_earned = 0;
		if($categories_stmt = mysqli_prepare($link2, "SELECT id, max_points, drop_after FROM `grade_categories` WHERE class = ?"))
		{
			mysqli_stmt_bind_param($categories_stmt, "i", $class_id);
			mysqli_stmt_execute($categories_stmt);
			mysqli_stmt_bind_result($categories_stmt, $category_id, $category_max_pts, $category_drop_after);
			while(mysqli_stmt_fetch($categories_stmt))
			{
				$class_max_points += $category_max_pts;
				$link3 = connect_db_read();
				// get the grade entries for the current category
				if($category_drop_after != 0){
					$grades_stmt = mysqli_prepare($link3, "SELECT id, points_earned, max_points FROM grades WHERE category = ? ORDER BY points_earned/max_points DESC LIMIT ?");
					if($grades_stmt)
						mysqli_stmt_bind_param($grades_stmt, "ii", $category_id, $category_drop_after);
				}else{
					$grades_stmt = mysqli_prepare($link3, "SELECT id, points_earned, max_points FROM grades WHERE category = ?");
					if($grades_stmt)
						mysqli_stmt_bind_param($grades_stmt, "i", $category_id);
				}
				if($grades_stmt)
				{
					$points = 0;
					$cat_max = 0;
					mysqli_stmt_execute($grades_stmt);
					mysqli_stmt_store_result($grades_stmt);
					if(mysqli_stmt_num_rows($grades_stmt) == 0)
						$class_max_points = $class_max_points - $category_max_pts;
					mysqli_stmt_bind_result($grades_stmt, $grade_id, $grade_pts, $grade_max);
					while(mysqli_stmt_fetch($grades_stmt))
					{
						$points = $points + $grade_pts; // at the completion of this loop $points holds the value of grades in that category
						$cat_max += $grade_max;
					}
					if($category_max_pts != 0 && $cat_max !=0)
						$category_points = ($points/$cat_max)*$category_max_pts;
					else
						$category_points = 0;
					//echo $category_points." ";
					$total_points_earned += $category_points;
				}else
					echo 'There was a database error. Try again later.';
				mysqli_close($link3);
			}
		}else
			echo 'There was a database error. Try again later.';
			
		$class_percent = $class_max_points == 0 ? -1 : $total_points_earned/$class_max_points;
		echo '					<div class="singleclass"><div class="class_name"><a href="class.php?o=details&amp;q='.$class_id.'">'.$class_name.'</a></div><div class="wrapper">'; echo $class_percent == -1 ? '<div class="class_points">No grades recorded</div>' : '<div class="class_points">'.number_format($total_points_earned, 2)."/".number_format($class_max_points).'</div>'; echo '<div class="class_letter_grade">'.print_letter_grade($class_percent)."</div></div></div>\n";
		mysqli_close($link2);
	}
	mysqli_close($link);
}

?>