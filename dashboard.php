<?php
include_once('common_functions.php');
sec_session_start();
if(login_check())
{
	// logged in correctly

	$link = connect_db_read();
	if($class_stmt = mysqli_prepare($link, "SELECT id, name, total_pts FROM `classes` WHERE student = ?"))
	{
		mysqli_stmt_bind_param($class_stmt, "i", $_SESSION['uid']);
		mysqli_stmt_execute($class_stmt);
		mysqli_stmt_bind_result($class_stmt, $class_id, $class_name, $class_total_pts);
	}
	
	// display the dashboard page
?>
	<head>
		<meta charset="urf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Your Ivy-League Dashboard</title>
		<script language="javascript" type="text/javascript">
			function showhide_cats(class_id) {
				var1 = document.getElementById('class_'+class_id);
				if (var1.style.display == 'none' || var1.style.display == '')
				{
					var1.style.display = 'block';
					document.getElementById('showhide_btn_'+class_id).innerHTML = '-';
				} else {
					var1.style.display = 'none';
					document.getElementById('showhide_btn_'+class_id).innerHTML = '+';
				}
			}
		</script>
	</head>
	<body id="dashboard">
		<div id="page_content">
			<div id="banner">
			<h1>Ivy-League STS</h1>
			</div>
			<div id="navbar">
				<p class="navcurrent">Home</p>
				<p class="navlink">About</p>
			</div>
			<div id="container">
				<div class="wrapper">
					<h1>Dashboard</h1>
					<div id="logout_block">
						<a href="logout.php">Logout</a>
					</div>
				</div>
				<div id="class_summary">
					<h2>Class Summary</h2>
					<hr>
<?php
					while(mysqli_stmt_fetch($class_stmt))
					{
						// for each class in the database print it's name (already obtained) and the current points over total
						// to get the total points we need to get the categories and add the max_points together
						$link2 = connect_db_read();
						$class_max_points = 0;
						$total_points_earned = 0;
						if($categories_stmt = mysqli_prepare($link2, "SELECT id, max_points FROM `grade_categories` WHERE class = ?"))
						{
							mysqli_stmt_bind_param($categories_stmt, "i", $class_id);
							mysqli_stmt_execute($categories_stmt);
							mysqli_stmt_bind_result($categories_stmt, $category_id, $category_max_pts);
							while(mysqli_stmt_fetch($categories_stmt))
							{
								$class_max_points += $category_max_pts;
								$link3 = connect_db_read();
								// get the grade entries for the current category
								if($grades_stmt = mysqli_prepare($link3, "SELECT id, points_earned, max_points FROM grades WHERE category = ?"))
								{
									$points = 0;
									$cat_max = 0;
									mysqli_stmt_bind_param($grades_stmt, "i", $category_id);
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
							}
						}else
							echo 'There was a database error. Try again later.';
					
						echo '					<div class="singleclass"><div class="class_name">'.$class_name.'</div><div class="wrapper"><div class="class_points">'.number_format($total_points_earned, 2)."/".number_format($class_max_points).'</div><div class="class_letter_grade">'.print_letter_grade($total_points_earned/$class_max_points)."</div></div></div>\n";
					}
					mysqli_close($link);
					mysqli_close($link2);
					mysqli_close($link3);
?>
				</div>
				<div id="point_summary">
					<h2>Point Summary</h2>
					<hr>
<?php
						$classes = get_user_classes($_SESSION['uid']);
						$reward_points = [];
						foreach($classes as $class_id)
						{
							// for each class we need to know the categories,
							// and the total number of points
							$categories = get_class_categories($class_id);
							$class_max_points = get_class_max_points($class_id);
							foreach($categories as $cat_id)
							{
								// in each category we need to know: how many entries exist in that category,
								// the total worth of that category, and how many points the student has earned so far
								
								// also query each assignment for it's grade (A, B, C, or D)
								$category_entries = num_in_category($cat_id);
								$category_weight = category_max_points($cat_id);
								
								$assignment_grades = fetch_assignment_percents($cat_id);
								$GPA_sum = 0;
								foreach($assignment_grades as $grade)
								{
									$GPA_sum += GPAfactor($grade);
								}
								
								if($category_entries > 0 && $class_max_points > 0)
								{
									$category_reward_points = ($category_weight*$GPA_sum*10)/($class_max_points*$category_entries);
								}else
									$category_reward_points = 0;
								
								// package all of the information into an array so it can be displayed sensibly by class and then indented category breakdown
								$reward_points[get_class_name($class_id)][category_name($cat_id)] = $category_reward_points;
							}
						}
						$total_rewards = 0;
						$k = 0;
						foreach($reward_points as $classname => $class)
						{
							$class_rewards = 0;
							foreach($class as $category)
							{
								$class_rewards += $category;
							}
							$total_rewards += $class_rewards;
							echo '					<div class="pt_line_wrapper"><button style="width:20px;height:20px;vertical-align:middle;padding:0;font-size:50%;" onclick="showhide_cats('.++$k.');return false;" id="showhide_btn_'.$k.'">+</button><div class="pt_class_line">'.$classname.'</div><div class="class_rewards">'.number_format($class_rewards, 2)."</div></div>\n";
							echo '					<div class="hide_cats" id="class_'.$k.'">';
							foreach($class as $categoryName => $category)
							{
								echo '						<div class="pt_cat_line"><div class="pt_cat_name">'.$categoryName.'</div><div class="pt_cat">'.number_format($category, 2)."</div></div>\n";
							}
							echo '					</div>';
						}
						//print_r($reward_points);
?>
				</div>
			</div>
		</div>
<?php
}else{
	// not logged in.
}
?>
	</body>
</html>