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
				$final_exam = false;
				foreach($categories as &$category)
				{
					$category['name'] = test_input($category['name']);
					if ($category['type'] = 'final exam' && $final_exam == false)
						$final_exam = true;
					else
						$type_err = "There can only be one category of type 'final exam' per class";
				}
				unset($category);
				
				// Insert the data into the table.
				if(empty($type_err) || empty($instructor_err) || empty($class_name_err))
				{
					
					$link = connect_db_insert();
					
					$stmt1 = mysqli_prepare($link, "INSERT INTO `classes` (`name`, `instructor`, `student`) VALUES (?, ?, ?)") or die(mysqli_error($link));
					mysqli_stmt_bind_param($stmt1, "ssi", $class_name, $instructor, $_SESSION['uid']);
					mysqli_stmt_execute($stmt1) or die(mysqli_error($link));
					
					$class_id = mysqli_insert_id($link);
					$stmt2 = mysqli_prepare($link, "INSERT INTO `grade_categories` (`name`, `class`, `type`, `max_points`) VALUES (?, ?, ?, ?)");
					foreach($categories as $category)
					{
						mysqli_stmt_bind_param($stmt2, "sisi", $category['name'], $class_id, $category['type'], $category['points']);
						mysqli_stmt_execute($stmt2) or die(mysqli_error($link));
					}
					mysqli_stmt_close($stmt1);
					mysqli_stmt_close($stmt2);
				}
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
			if($_SERVER['REQUEST_METHOD'] != 'POST' || !empty($class_name_err) || !empty($instructor_err) || !empty($type_err)){
			?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Add a Class</title>
		<script language="javascript" type="text/javascript">
			function toggleSpecialOptions(lineNum)
			{
				var selectedOption = document.getElementById("category_special_"+lineNum).value;
				var nFld = document.getElementById("category_n_"+lineNum);
				var nFldLabel = document.getElementById("category_n_label_"+lineNum);
				if(selectedOption == "dropAfterN" || selectedOption == "dropLowestN")
				{
					nFld.style.display = "inline";
					nFldLabel.style.display = "inline";
					nFld.required = true;
				}else{
					nFld.style.display = "none";
					nFldLabel.style.display = "none";
					nFld.removeAttribute("required")
				}
			}
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
				
				var new_type_label = document.createElement("label");
				new_type_label.htmlFor = "category_type_"+nextLine;
				var label_text = document.createTextNode("Type: ");
				new_type_label.appendChild(label_text);
				
				var new_pts_label = document.createElement("label");
				new_pts_label.htmlFor = "category_points_"+nextLine;
				var label_text = document.createTextNode("Points: ");
				new_pts_label.appendChild(label_text);
				
				var new_type_fld = document.createElement("select");
				new_type_fld.required = true;
				new_type_fld.name = "category["+nextLine+"][type]";
				new_type_fld.id = "category_type_"+nextLine;
				var types = ['homework','quiz','exam','lab','in-class','assignment','project','other','final exam'];
				for(var t in types)
				{
					var new_option = document.createElement("option");
					new_option.value = types[t];
					var optionText = document.createTextNode(types[t]);
					new_option.appendChild(optionText);
					new_type_fld.appendChild(new_option);
				}
				
				var new_pts_fld = document.createElement("input");
				new_pts_fld.type = "number";
				new_pts_fld.id = "category_points_"+nextLine;
				new_pts_fld.min = "1";
				new_pts_fld.name = "category["+nextLine+"][points]";
				new_pts_fld.style.width = "5em";
				
				var new_special_label = document.createElement("label");
				new_special_label.htmlFor = "category_special_"+nextLine;
				var label_text = document.createTextNode("Special: ");
				new_special_label.appendChild(label_text);
				
				var new_special_fld = document.createElement("select");
				new_special_fld.name = "category["+nextLine+"][special]";
				new_special_fld.id = "category_special_"+nextLine;
				new_special_fld.addEventListener("change", function(){toggleSpecialOptions(""+nextLine);}, false);
				
				var new_n_label = document.createElement("label");
				new_n_label.htmlFor = "category_n_"+nextLine;
				new_n_label.id = "category_n_label_"+nextLine;
				new_n_label.style.display="none";
				var label_text = document.createTextNode("N: ");
				new_n_label.appendChild(label_text);
				
				// create the options for the special select box (4)
				var new_option = document.createElement("option");
				new_option.value = "";
				new_special_fld.appendChild(new_option);
				
				var new_option = document.createElement("option");
					new_option.value = "dropAfterN";
					var optionText = document.createTextNode("Drop After N Assignments");
					new_option.appendChild(optionText);
					new_special_fld.appendChild(new_option);
					
				var new_option = document.createElement("option");
					new_option.value = "dropLowestN";
					var optionText = document.createTextNode("Drop Lowest N Assignments");
					new_option.appendChild(optionText);
					new_special_fld.appendChild(new_option);
					
				var new_option = document.createElement("option");
					new_option.value = "finalReplacesLowestExam";
					var optionText = document.createTextNode("Final Exam Replaces Lowest Exam");
					new_option.appendChild(optionText);
					new_special_fld.appendChild(new_option);
				
				var new_n_fld = document.createElement("input");
				new_n_fld.type = 'number';
				new_n_fld.id = 'category_n_'+nextLine;
				new_n_fld.min = "1";
				new_n_fld.step = "1";
				new_n_fld.name = "category["+nextLine+"][n]";
				new_n_fld.style.display = "none";
				
				
				var new_remove_link = document.createElement("a");
				new_remove_link.href = "#";
				new_remove_link.setAttribute("onclick", "destroy_category("+nextLine+")");
				
				var new_remove_text = document.createTextNode("Remove");
				new_remove_link.appendChild(new_remove_text);
				
				new_line.appendChild(new_name_label);
				new_line.appendChild(new_name_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_type_label);
				new_line.appendChild(new_type_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_pts_label);
				new_line.appendChild(new_pts_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_special_label);
				new_line.appendChild(new_special_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_n_label);
				new_line.appendChild(new_n_fld);
				var space = document.createTextNode(" ");
				new_line.appendChild(space);
				new_line.appendChild(new_remove_link);
				
				container.appendChild(new_line);
			}
		</script>
	</head>
	<body id="dashboard">
		<div id="page_content">
			<form id="add_class" style="margin-left:auto;margin-right:auto;width:80%;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?o=add'; ?>" method="post">
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
						<div class="category_line_wrapper" id="category_line_1"><label for="category_name_1">Category Name: </label><input type="text" id="category_name_1" name="category[1][name]"> <label for="category_type_1">Type: </label><select name="category[1][type]" required><option value="homework">homework</option><option value="quiz">quiz</option><option value="exam">exam</option><option value="lab">lab</option><option value="in-class">in-class</option><option value="assignment">assignment</option><option value="project">project</option><option value="other">other</option></select> <label for="category_points_1">Points: </label><input type="number" id="category_points_1" name="category[1][points]" min="1" style="width: 5em"> <label for="category_special_1">Special: </label><select id="category_special_1" name="category[1][special]" onchange="toggleSpecialOptions(1)"><option value="" selected></option><option value="dropAfterN">Drop After N Assignments</option><option value="dropLowestN">Drop Lowest N Assignments</option><option value="finalReplacesLowestExam">Final Exam Replaces Lowest Exam</option></select> <label for="category_n_1" id="category_n_label_1" style="display: none;">N: </label> <input type="number" id="category_n_1" name="category[1][n]" min="1" step="1" style="display: none;" /> <a href="#" onclick="destroy_category(1);return false;">Remove</a></div>
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
				if(!empty($_POST['edit-submit']))
				{
					// clean up the variables
					$link = connect_db_update();
					$action = array();
					$action['result'] = null;
					if(empty($_POST['description']))
					{
						$action['result'] = 'error';
						$action['text'] = 'Description field is required and can not be blank.';
					}else{
						if(empty($_POST['points']))
						{
							$action['result'] = 'error';
							$action['text'] = 'Points field is required and can not be blank.';
						}else{
							if(empty($_POST['max_points']))
							{
								$action['result'] = 'error';
								$action['text'] = 'Max points field is required and can not be blank.';
							}else{
								// all fields not blank, no errors
								
								// clean fields and validate
								$newDescription = mysqli_real_escape_string($link, str_replace(';','',$_POST["description"]));
								$points = mysqli_real_escape_string($link, $_POST["points"]);
								$maxPoints = mysqli_real_escape_string($link, $_POST["max_points"]);
								if($stmt = mysqli_prepare($link, "UPDATE grades SET description=?, points_earned=?, max_points=? WHERE id=?"))
								{
									mysqli_stmt_bind_param($stmt, "sddi", $newDescription, $points, $maxPoints, $_POST["hiddenID"]);
									if(!mysqli_stmt_execute($stmt))
									{
										$action['result'] = 'error';
										$action['text'] = 'Row not updated. Reason: '.mysqli_error($link);
									}else{
										if(mysqli_stmt_affected_rows($stmt) == 1)
										{
											$action['result'] = 'success';
											$action['text'] = 'Updated Successfully.';
										}else{
											$action['result'] = 'unknown';
											$action['text'] = 'No errors were detected but zero rows were affected by the command. Reason: '.mysqli_error($link);
										}
									}
									mysqli_stmt_close($stmt);
								}else{
									$action['result'] = 'error';
									$action['text'] = 'Row not updated. Reason: '.mysqli_error($link);
								}
							}
						}
					}
					mysqli_close($link);
				}
				if(!empty($_POST['add-submit']))
				{
					$link = connect_db_insert();
					$action = array();
					$action['result'] = null;
					if(empty($_POST['description']))
					{
						$action['result'] = 'error';
						$action['text'] = 'Description field is required and can not be blank.';
					}else{
						if(empty($_POST['points']))
						{
							$action['result'] = 'error';
							$action['text'] = 'Points field is required and can not be blank.';
						}else{
							if(empty($_POST['max_points']))
							{
								$action['result'] = 'error';
								$action['text'] = 'Max points field is required and can not be blank.';
							}else{
								// all fields not blank, no errors
								
								// clean fields and validate
								$newDescription = mysqli_real_escape_string($link, str_replace(';','',$_POST["description"]));
								$points = mysqli_real_escape_string($link, $_POST["points"]);
								$maxPoints = mysqli_real_escape_string($link, $_POST["max_points"]);
								
								if($stmt = mysqli_prepare($link, "INSERT INTO grades (category, description, points_earned, max_points) VALUES (?,?,?,?)"))
								{
									mysqli_stmt_bind_param($stmt, "isdd", $_POST["catID"], $newDescription, $points, $maxPoints);
									if(!mysqli_stmt_execute($stmt))
									{
										$action['result'] = 'error';
										$action['text'] = 'Row not inserted into database. Reason: '.mysqli_error($link);
									}else{
										if(mysqli_stmt_affected_rows($stmt) == 1)
										{
											$action['result'] = 'success';
											$action['text'] = 'Grade Added Successfully.';
										}else{
											$action['result'] = 'unknown';
											$action['text'] = 'No errors were detected but zero rows were affected by the command. Reason: '.mysqli_error($link);
										}
									}
									mysqli_stmt_close($stmt);
								}else{
									$action['result'] = 'error';
									$action['text'] = 'Row not inserted into database. Reason: '.mysqli_error($link);
								}
							}
						}
					}
					mysqli_close($link);
				}
				if(!empty($_POST['cat-submit']))
				{
					// clean up the variables
					$link = connect_db_insert();
					$action = array();
					$action['result'] = null;
					if(empty($_POST['catName']))
					{
						$action['result'] = 'error';
						$action['text'] = 'Name field is required and can not be blank.';
					}else{
						if(empty($_POST['categoryType']))
						{
							$action['result'] = 'error';
							$action['text'] = 'Type field is required and can not be blank.';
						}else{
							if(empty($_POST['category_points']))
							{
								$action['result'] = 'error';
								$action['text'] = 'Points field is required and can not be blank.';
							}else{
								if(($_POST['category_special'] == "dropAfterN" || $_POST['category_special'] == "dropLowestN") && empty($_POST['category_n']))
								{
									$action['result'] = 'error';
									$action['text'] = 'N field is required when visible depending on the value of the special field.';
								}else{
									// all required fields filled out, no errors
									
									// validate and clean the fields
									$newCatName = mysqli_real_escape_string($link, $_POST['catName']);
									$newCatType = mysqli_real_escape_string($link, $_POST['categoryType']);
									$newPoints = mysqli_real_escape_string($link, $_POST['category_points']);
									$newN = mysqli_real_escape_string($link, $_POST['category_n']);
									
									$dropLowest = 0;
									$dropAfter = 0;
									$finalReplaces = false;
									switch($_POST['category_special'])
									{
										case "dropAfterN":
											$dropAfter = $newN;
										break;
										
										case "dropLowestN":
											$dropLowest = $newN;
										break;
										
										case "finalReplacesLowestExam":
											$finalReplaces = true;
										break;
									}
									
									if($stmt = mysqli_prepare($link, "INSERT INTO grade_categories (name, class, type, max_points, lowest_drop, drop_after, finalReplacesLowExam) VALUES (?, ?, ?, ?, ?, ?, ?)"))
									{
										mysqli_stmt_bind_param($stmt, 'sisiiii', $newCatName, $_POST['classID'], $newCatType, $newPoints, $dropLowest, $dropAfter, $finalReplaces);
										if(!mysqli_stmt_execute($stmt))
										{
											$action['result'] = 'error';
											$action['text'] = 'Row not inserted into the database. Reason: '.mysqli_error($link);
										}else{
											if(mysqli_stmt_affected_rows($stmt) == 1)
											{
												$action['result'] = 'success';
												$action['text'] = 'Category Created Successfully.';
											}else{
												$action['result'] = 'unknown';
												$action['text'] = 'No errors were detected but zero rows were returned by the database. Reason: '.mysqli_error($link);
											}
										}
										mysqli_stmt_close($stmt);
									}else{
										$action['result'] = 'error';
										$action['text'] = 'Row not inserted into the database. Reason: '.mysqli_error($link);
									}
								}
							}
						}
					}
					mysqli_close($link);
				}
			}
?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Class Details</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script> 
		$(document).ready(function(){
			$(".disappear").delay(2500).slideUp(1200, "swing");
		});
		</script> 
	</head>
	<body id="class_details">
	<img src="images/lockout.png" width="100%" height="200%" id="lockoutImg" style="position: absolute;left: 0px;top: 0px;z-index: 100;display: none;"/>
	
	<div id="scroll_form_edit_grade" class="row">
	<div class="col-3 col-m-1"></div>
	<div class="col-6 col-m-10">
		<form method="POST" action="" style="margin-left: auto; margin-right: auto;width: 85%;background-color: #465150;color: white;border: #aaa solid 2px;border-radius: 10px 10px 10px 10px; -moz-border-radius: 10px 10px 10px 10px; -webkit-border-radius: 10px 10px 10px 10px;">
		<img id="edit-hideBtn" height="24px" width="24px" src="images/hidebtn.png" alt="Close form" align="right" style="position:relative;top:10px;right:10px;"/>
		<div class="centered" style="width: 75%;margin-left: auto; margin-right:auto;">
			<label for="description">Description: </label><input type="text" id="editFormDesc" name="description"/><br>
			Points: <input type="number" id="editFormEarned" name="points" size="4" min="0" step="0.1" style="width:4em;"/> / <input type="number" id="editFormMax" name="max_points" min="0" step="0.1" size="4" style="width:4em;"/>
			<br>
			<br>
			<input name="edit-submit" type="submit" value="Edit Grade"/><input type="hidden" name="hiddenID" id="hiddenID" value="" />
		</div>
		</form>
	</div>
	<div class="col-3 col-m-1"></div>
	</div>
	
	
	<div id="scroll_form_add_assignment" class="row">
	<div class="col-3 col-m-1"></div>
	<div class="col-6 col-m-10">
		<form method="POST" action="" style="margin-left: auto; margin-right: auto;width: 85%;background-color: #465150;color: white;border: #aaa solid 2px;border-radius: 10px 10px 10px 10px; -moz-border-radius: 10px 10px 10px 10px; -webkit-border-radius: 10px 10px 10px 10px;">
		<img id="add-hideBtn" height="24px" width="24px" src="images/hidebtn.png" alt="Close form" align="right" style="position:relative;top:10px;right:10px;"/>
		<div class="centered" style="width: 75%;margin-left: auto; margin-right:auto;">
			<div id="cat_label" style="font-size: 120%;"></div>
			<label for="description">Description: </label><input type="text" id="addFormDesc" name="description"/><br>
			Points: <input type="number" id="addFormEarned" name="points" min="0" step="0.05" size="4" style="width:4em;"/> / <input type="number" id="addFormMax" name="max_points" min="0" step="0.05" size="4" style="width:4em;"/>
			<br>
			<br>
			<input name="add-submit" type="submit" value="Add Assignment"/><input type="hidden" name="catID" id="catID" value="" />
		</div>
		</form>
	</div>
	<div class="col-3 col-m-1"></div>
	</div>
	
	<div id="scroll_form_add_category" class="row">
	<div class="col-3 col-m-1"></div>
	<div class="col-6 col-m-10">
		<form method="POST" action="" style="margin-left: auto; margin-right: auto;width: 85%;background-color: #465150;color: white;border: #aaa solid 2px;border-radius: 10px 10px 10px 10px; -moz-border-radius: 10px 10px 10px 10px; -webkit-border-radius: 10px 10px 10px 10px;">
		<img id="cat-hideBtn" height="24px" width="24px" src="images/hidebtn.png" alt="Close form" align="right" style="position:relative;top:10px;right:10px;"/>
		<div class="centered" style="width: 96%;margin-left: auto; margin-right:auto;">
		<label for="catName">Category Name: </label><input type="text" id="addCatName" name="catName" required/><br>
		<label for="categoryType">Type: </label><select name="categoryType" required>
			<option value="homework">homework</option>
			<option value="quiz">quiz</option>
			<option value="exam">exam</option>
			<option value="lab">lab</option>
			<option value="in-class">in-class</option>
			<option value="assignment">assignment</option>
			<option value="project">project</option>
			<option value="other">other</option>
		</select>
		<label for="category_points">Points: </label><input type="number" id="category_points" name="category_points" min="1" style="width: 5em" required><br>
		<label for="category_special">Special: </label><select id="category_special" name="category_special" onchange="toggleSpecialOptions()">
			<option value="" selected></option><option value="dropAfterN">Drop After N Assignments</option>
			<option value="dropLowestN">Drop Lowest N Assignments</option>
			<option value="finalReplacesLowestExam">Final Exam Replaces Lowest Exam</option>
		</select><br>
		<label for="category_n" id="category_n_label" style="display: none;">N: </label><input type="number" id="category_n" name="category_n" min="1" step="1" style="display: none;" />
		<br>
		<br>
		<input name="cat-submit" type="submit" value="Add Category"/><input type="hidden" name="classID" id="classID" value="'.$_GET['q'].'" /><br>
		</div>
		</form>
	</div>
	</div>
		
		<script>
			function toggleSpecialOptions(){
				var label = document.getElementById("category_n_label");
				var n = document.getElementById("category_n");
				var selectedOption = document.getElementById("category_special").value;
				
				if(selectedOption == "dropAfterN" || selectedOption == "dropLowestN")
				{
					label.style.display = "inline";
					n.style.display = "inline";
					n.required = true;
				}else{
					n.style.display = "none";
					n.value = "";
					label.style.display = "none";
					n.removeAttribute("required");
				}
			}
		</script>
	
		<div id="page_content">
			<div class="row">
				<div id="banner" class="col-12 col-m-12">
					<h1>Ivy-League</h1>
					<h3>Scholarship Tracking System</h3>
				</div>
			</div>
			<div class="row">
				<div id="navbar" class="col-12 col-m-12" style="padding-top: 0px; padding-bottom: 0px;">
					<?php print_navbar_items(); ?>
				</div>
			</div>
<?php
	if(isset($action))
	switch($action['result'])
	{
		case "success":
			echo '			<div class="disappear" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
		break;
		
		case "unknown":
			echo '			<div class="disappear" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
		break;
		
		case "error":
			echo '			<div class="disappear" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
		break;
	}
?>
			<div id="container">
				<div class="row">
					<div class="col-7 col-m-7">
					<h1>Class Details</h1>
					</div>
					<div class="col-5 col-m-5">
					<div id="right_float_wrapper">
						<div id="logout_block" style="margin-right: 0px;">
							<a href="logout.php">Logout</a>
						</div>
					</div>
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
				<div class="row">
				<div class="col-12 col-m-12">
				<h2>Details for <?php echo $class_name; ?> </h2>
				<h3>Instructor: <?php echo $class_instructor; ?></h3>
				<h3><?php print_percentage($class_query_id); ?></h3>
				<h3>Grades:</h3>
				</div>
				</div>
				<div class="row">
				<div class="col-6 col-m-12">
				<div id="categories_section">
<?php
					mysqli_stmt_free_result($stmt);
					$class_categories = class_categories_names($class_query_id);
					foreach($class_categories as $cat_id => $category)
					{
						echo '<p class="category"><a href="class.php?o=category&amp;q='.$cat_id.'" target="category_details_window">'.$category.'</a> <img src="images/insert.gif" width="16px" height="16px" class="insert-grade" catID="'.$cat_id.'" catName="'.$category.'" alt="Add assignment to this category"/></p>';
					}

					echo '				</div>
				</div>
				<div class="col-6 col-m-12">
				<iframe id="category_details_window" name="category_details_window" style="width:100%;float:right;display:inline-block;border:0px;" src="blank.html" srcdoc=\'<!DOCTYPE html><html lang="en" dir="ltr"><head><meta charset="utf-8"><link rel="stylesheet" type="text/css" href="custom.css.php"><title></title></head><body id="category_details"></body></html>\' >Your browser does not support frames</iframe>
				</div>
				</div>
				<br>
				<br>
				<a id="addCat" classID="'.$_GET['q'].'">Add Category</a>
				<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
				<script src="add-assign.js"></script>'."\n";

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
?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Category Details</title>
		<script>
		function resizeIframe(iframeID)
		{
			var iframe = window.parent.document.getElementById(iframeID);
			var container = document.getElementById('category_details_content');
			iframe.style.height = (container.offsetHeight+20) + 'px';
		}
	</script>
	</head>
	<body onload="resizeIframe('category_details_window')">
		<div id="category_details_content">
<?php
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
						echo '		<h3>Number of assignments: '.$results.'</h3>';
						echo '		<h3 style="display: inline-block;margin: 0px;">Points Awarded: '.category_pts_earned($category_query_id).' / '.category_pts_offered($category_query_id).'</h3><h3 style="display: inline-block;margin: 0px 20px 0px 20px;">Average: '.number_format(category_pts_earned($category_query_id)/$results, 2)."</h3><br>\n";
						echo '		<h3 style="display: inline-block;margin: 0px;">Realtime Grade Earned for this category: '.number_format(category_pts_earned($category_query_id)/category_pts_offered($category_query_id)*category_max_points($category_query_id), 2).' / '.category_max_points($category_query_id)."</h3><br>\n";
						while(mysqli_stmt_fetch($grades_stmt))
						{
							echo '		<p style="display:inline-block;line-height:5%;">'.$description.' .......... '.$points.' out of '.$max_points.' points.</p> <a class="edit-grade" val="'.$points.';'.$max_points.';'.$description.';'.$id.'">edit</a><br>
';
						}
					}else
						echo 'No assignments entered in this category.';
				}
			}else
				echo 'No category id provided';
?>
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="app.js"></script>
<?php
		break; // end category details case
	}
}else{
	// Error: User not logged in
	echo 'You are not logged in! ACCESS DENIED!';
}

?>
	</body>
</html>