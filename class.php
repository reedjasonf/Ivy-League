<?php
include_once('common_functions.php');
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
	switch($_GET['o'])
	{
		case "add":
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				// check the passed data
				if(empty($_POST['new_class_name']))
					$class_name_err = "Class Title required.";
				else{
					$class_name = test_input($_POST['new_class_name']);
				}
				
				if(empty($_POST['new_class_instructor']))
					$instructor_err = "Class Instructor required.";
				else{
					$instructor = test_input($_POST['new_class_instructor']);
				}
				
				$categories = $_POST['category'];
				foreach($categories as &$category)
				{
					$category['name'] = test_input($category['name']);
				}
				unset($category);
				
				// Insert the data into the table.
				
				$link = connect_db_insert();
				
				$stmt1 = mysqli_prepare($link, "INSERT INTO `classes` (`name`, `instructor`, `student`) VALUES (?, ?, ?)") or die(mysqli_error($link));
				mysqli_stmt_bind_param($stmt1, "ssi", $class_name, $instructor, $_SESSION['uid']);
				mysqli_stmt_execute($stmt1) or die(mysqli_error($link));
				
				$class_id = mysqli_insert_id($link);
				$stmt2 = mysqli_prepare($link, "INSERT INTO `grade_categories` (`name`, `class`, `max_points`) VALUES (?, ?, ?)");
				foreach($categories as $category)
				{
					mysqli_stmt_bind_param($stmt2, "sii", $category['name'], $class_id, $category['points']);
					mysqli_stmt_execute($stmt2) or die(mysqli_error($link));
				}
				mysqli_stmt_close($stmt1);
				mysqli_stmt_close($stmt2);
?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<script language="javascript" type="text/javascript">
			function close_window() {
				window.opener.location.reload();
				window.close();
				window.opener.focus();
			}
		</script>
	</head>
	<body onload="close_window();">
	</body>
</html>
<?php
			}
			if($_SERVER['REQUEST_METHOD'] != 'POST' || !empty($class_name_err) || !empty($instructor_err)){
			?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Add a Class</title>
		<script language="javascript" type="text/javascript">
			function destroy_category(cat_id)
			{
				var container = document.getElementById('category_container');
				var line = document.getElementById('category_line_'+cat_id);
				container.removeChild(line);
			}
			function add_category()
			{
				var container = document.getElementById('category_container');
				
				var i = 1;
				while(document.getElementById('category_line_'+i)) {
					i++;
				}
				var nextLine = i;
				
				var new_line = document.createElement("div");
				
				new_line.className = "category_line_wrapper";
				new_line.id = "category_line_"+(nextLine);
				
				var new_name_label = document.createElement("label");
				new_name_label.htmlFor = "category_name_"+nextLine;
				var label_text = document.createTextNode("Category Name: ");
				new_name_label.appendChild(label_text);
				
				var new_name_fld = document.createElement("input");
				new_name_fld.type = "text";
				new_name_fld.id = "category_name_"+nextLine;
				new_name_fld.name = "category["+nextLine+"][name]";
				
				var new_pts_label = document.createElement("label");
				new_pts_label.htmlFor = "category_name_"+nextLine;
				var label_text = document.createTextNode("Points: ");
				new_pts_label.appendChild(label_text);
				
				var new_pts_fld = document.createElement("input");
				new_pts_fld.type = "number";
				new_pts_fld.id = "category_points_"+nextLine;
				new_pts_fld.min = "1";
				new_pts_fld.name = "category["+nextLine+"][points]";
				
				var new_remove_link = document.createElement("a");
				new_remove_link.href = "#";
				new_remove_link.setAttribute("onclick", "destroy_category("+nextLine+")");
				
				var new_remove_text = document.createTextNode("Remove");
				new_remove_link.appendChild(new_remove_text);
				
				new_line.appendChild(new_name_label);
				new_line.appendChild(new_name_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_pts_label);
				new_line.appendChild(new_pts_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_remove_link);
				
				container.appendChild(new_line);
			}
		</script>
	</head>
	<body id="dashboard">
		<div id="page_content">
			<form id="add_class" style="margin-left:auto;margin-right:auto;width:60%;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?o=add'; ?>" method="post">
				<fieldset>
					<legend lang="en" dir="ltr">Class Information</legend>
					Class Title* <input type="text" name="new_class_name" id="new_class_name" placeholder="(ex. 'Calculus I')" required><?php echo isset($class_name_err) ? $class_name_err : ""; ?><br>
					Instructor Name* <input type="text" name="new_class_instructor" id="new_class_instructor" placeholder="(ex 'Dr. Clark')" required><?php echo isset($instructor_err) ? $instructor_err : ""; ?><br>
				</fieldset>
				<fieldset>
					<legend lang="en" dir="ltr">Categories</legend>
					<h3><u>Instructions</u></h3>
					<p>Reference your course syllabus for grading information. For example, if your syllabus says that there will be four 100 point exams you may
					either choose to create one category called 'Exams' worth 400 points to put the scores into or you may create four categories called 'Exam 1', 
					'Exam 2', 'Exam 3' and 'Exam 4', each worth 100 points.</p>
					<p>If your syllabus says that homework is worth 100 points for the entire class but does not mention how many assignments, or the number of 
					assignment is very large you should just create a category called 'Homework' and put all of the grades in rather than creating categories for 
					each assignment.</p>
					<p>You can create or delete categories later if you don't want to put them all in now.</p>
					<div id="category_container">
						<div class="category_line_wrapper" id="category_line_1"><label for="category_name_1">Category Name: </label><input type="text" id="category_name_1" name="category[1][name]"> <label for="category_points_1">Points: </label><input type="number" id="category_points_1" name="category[1][points]" min="1"> <a href="#" onclick="destroy_category(1);return false;">Remove</a></div>
					</div>
					<a href="#" onclick="add_category();return false;">Create Another Category</a>
				</fieldset>
				<br>
				<input type="submit" value="Create Class"> <input type="reset" value="Cancel" onclick="window.close();window.opener.focus();return false;">
			</form>
		</div>
<?php
			}
		break; // end add case
		
		case "details":
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$link = connect_db_update();
				$stmt = mysqli_prepare($link, "UPDATE grades SET description=?, points_earned=?, max_points=? WHERE id=?") or die(mysqli_error($link));
				mysqli_stmt_bind_param($stmt, "siii", $_POST["description"], $_POST["points"], $_POST["max_points"], $_POST["hiddenID"]);
				mysqli_stmt_execute($stmt) or die(mysqli_error($link));
			}
?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Class Details</title>
	</head>
	<body id="class_details">
	<img src="images/lockout.png" width="100%" height="100%" id="lockoutImg" style="position: absolute;left: 0px;top: 0px;z-index: 100;display: none;"/>
	<div id="scroll_form_edit_grade">
		<form method="POST" action="">
		<img id="hideBtn" height="24px" width="24px" src="images/hidebtn.png" alt="Close form" align="right" style="position:relative;top:-25px;"/>
		<div class="centered">
			<label for="description">Description: </label><input type="text" id="editFormDesc" name="description"/><br>
			Points: <input type="number" id="editFormEarned" name="points" min="0" size="4" style="width:4em;"/> / <input type="number" id="editFormMax" name="max_points" min="0" size="4" style="width:4em;"/>
			<br>
			<br>
			<input type="submit" value="Edit Grade"/><input type="hidden" name="hiddenID" id="hiddenID" value="" />
		</div>
		</form>
	</div>
		<div id="page_content">
			<div id="banner">
				<h1>Ivy-League STS</h1>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container" style="min-height:75%">
				<div class="wrapper">
					<h1>Class Details</h1>
					<div id="logout_block">
						<a href="logout.php">Logout</a>
					</div>
				</div>
<?php
		if(isset($_GET['q']))
		{
			// q will hold the class_id that we are Querying
			$class_query_id = $_GET['q'];
			$link = connect_db_read();
			
			if($stmt = mysqli_prepare($link, "SELECT `name`, `instructor`, `total_pts` from `classes` WHERE id = ? and student = ?"))
			{
				mysqli_stmt_bind_param($stmt, "ii", $class_query_id, $_SESSION['uid']);
				
				mysqli_stmt_execute($stmt);
				mysqli_stmt_bind_result($stmt, $class_name, $class_instructor, $class_points);
				$result = mysqli_use_result($link);
				if(mysqli_stmt_fetch($stmt))
				{
?>
				<h2>Details for <?php echo $class_name; ?> </h2>
				<h3>Instructor: <?php echo $class_instructor; ?></h3>
				<h3>Grades:</h3>
				<div id="categories_section">
<?php
					mysqli_stmt_free_result($stmt);
					$class_categories = class_categories_names($class_query_id);
					foreach($class_categories as $cat_id => $category)
					{
						echo '<p class="category"><a href="class.php?o=category&amp;q='.$cat_id.'" target="category_details_window">'.$category.'</a> <a href="'.$cat_id.'"><img src="images/insert.gif" width="16px" height="16px"/></a></p>';
					}
					/*if($categories_stmt = mysqli_prepare($link, "SELECT id, name, max_points FROM `grade_categories` WHERE class = ?"))
					{
						mysqli_stmt_bind_param($categories_stmt, "i", $class_query_id);
						mysqli_stmt_execute($categories_stmt);
						mysqli_stmt_bind_result($categories_stmt, $category_id, $category_name, $category_max_pts);
						$link2 = connect_db_read();
						if($grade_stmt = mysqli_prepare($link2, "SELECT points_earned, max_points FROM `grades` WHERE category = ?"))
						while(mysqli_stmt_fetch($categories_stmt))
						{
							echo '<p class="category">'.$category_name.'</p>';
							mysqli_stmt_bind_param($grade_stmt, "i", $category_id);
							mysqli_stmt_execute($grade_stmt);
							mysqli_stmt_bind_result($grade_stmt, $points, $max_points);
							$i = 1;
							while(mysqli_stmt_fetch($grade_stmt))
							{
								echo '<p class="assignment_grade">Entry # '.$i++.': '.$points.'/'.$max_points.'</p>';
							}
						}
						
						foreach($categories as $category)
						{
						
						}
					}else
						echo 'Fatal Database Error! Try again later.'.mysqli_error($link);*/
?>
				</div>
				<iframe id="category_details_window" name="category_details_window" style="min-height:30em;margin-right:2%;width:45%;float:right;display:inline-block;border:0px;" src="blank.html" srcdoc="<!DOCTYPE html><html lang='en' dir='ltr'><head><meta charset='utf-8'><link rel='stylesheet' type='text/css' href='custom.css.php'><title></title></head><body id='category_details'></body></html>" >Your browser does not support frames</iframe>
				<br>
				<br>
				<a href="">Add Category</a>
<?php
				}else{
					echo 'Class details couldn\'t be found';
				}
			}else{
				echo 'Fatal Database Error! Try again later.';
			}
		}else
			echo 'No class id provided';
?>
			</div>
		</div>
<?php
		break; // end details case
		case "category":
			echo '	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Category Details</title>
	</head>
	<body>';
			if(isset($_GET['q']))
			{
				$category_query_id = $_GET['q'];
				$link = connect_db_read();
				
				if($grades_stmt = mysqli_prepare($link, "SELECT id, points_earned, max_points, description FROM grades WHERE category = ?"))
				{
					$k = 1;
					mysqli_stmt_bind_param($grades_stmt, "i", $category_query_id);
					mysqli_stmt_execute($grades_stmt);
					mysqli_stmt_store_result($grades_stmt);
					$results = mysqli_stmt_num_rows($grades_stmt);
					mysqli_stmt_bind_result($grades_stmt, $id, $points, $max_points, $description);
					if($results >= 1){
						echo '<h3>Number of assignments: '.$results.'</h3>';
						echo '<h3 style="display: inline-block;margin: 0px;">Points Awarded: '.category_pts_earned($category_query_id).'</h3><h3 style="display: inline-block;margin: 0px 20px 0px 20px;">Average: '.number_format(category_pts_earned($category_query_id)/$results, 2).'</h3>';
						echo '<h3>Points Offered: '.category_pts_offered($category_query_id).'</h3>';
						while(mysqli_stmt_fetch($grades_stmt))
						{
							echo '		<p style="display:inline-block;line-height:5%;">Assignment '.$k++.': '.$description.' .......... '.$points.' out of '.$max_points.' points.</p> <a class="edit-grade" val="'.$points.';'.$max_points.';'.$description.';'.$id.'">edit</span><br>
';
						}
					}else
						echo 'No assignments entered in this category.';
				}
			}else
				echo 'No category id provided';
echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="app.js"></script>';
		break; // end category details case
	}
}else{
	// Error: User not logged in
	echo 'You are not logged in! ACCESS DENIED!';
}

?>
	</body>
</html>