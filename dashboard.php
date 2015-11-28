<?php
include_once('common_functions.php');
include_once('grade_functions.php');
sec_session_start();
if(COMPRESSION == TRUE){
	ob_start("ob_gzhandler");
}
?>
<!DOCTYPE html>
<html lang='en' dir='ltr'>
<?php
if(login_check())
{
	// logged in correctly	
	// display the dashboard page
?>
	<head>
		<meta charset="urf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
			<h1>Ivy-League</h1>
			<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container">
				<div class="wrapper">
					<h1>Dashboard</h1>
					<div id="right_float_wrapper">
<?php
	if($_SESSION["permissions"] != 0)
		echo '						<div id="admin_block">
							<a href="admin_panel.php">Admin</a>
						</div>
';
?>
						<div id="logout_block">
							<a href="logout.php">Logout</a>
						</div>
					</div>
				</div>
				<div id="class_summary">
					<h2>Class Summary</h2>
					<hr>
<?php
					print_summary_all_classes($_SESSION['uid']);
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
				<br>
				<br>
				<a href="class.php?o=add" target="add_class_window">Add A Class</a>
			</div>
		</div>
<?php
}else{
	// not logged in.
?>
	<head>
		<meta charset="urf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Error: Not Logged In</title>
	</head>
	<body id="loginError">
		<div id="page_content">
			<div id="banner">
			<h1>Ivy-League</h1>
			<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container">
				<h2>You're not even logged in.</h2>
				<h3>Security Error: You are trying to access a secure page while not logged in.</h3>
<?php
	//echo $_SESSION['uid'].' '.$_SESSION['login_string'];
	$entry = "Direct visit to ".$_SERVER['PHP_SELF']." from IP ".$_SERVER['REMOTE_ADDR'].". [".date_with_micro('Y-m-d H:i:s:u')."]\n";
	file_put_contents("logs/security.txt", $entry, FILE_APPEND | LOCK_EX);
?>
			</div>
		</div>
<?php
}
?>
	</body>
</html>