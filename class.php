<!DOCTYPE html>
<html lang='en' dir='ltr'>
<?php
include_once('common_functions.php');
sec_session_start();

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
		exit;
	}
}else{
	// Error: User not logged in
	echo 'You are not logged in! ACCESS DENIED!';
}

?>
	</body>
</html>