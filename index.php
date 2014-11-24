<?php
include_once('common_functions.php');
?>
<!DOCTYPE html>
<html lang='en' dir='ltr'>
<?php
$username_err = $password_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// preprocess username, and password fields. Password should be empty and "hashed" should match db password.
	if(empty($_POST['username_fld']))
	{
		$username_err = "Username is required.";
	}else{
		$username = test_input($_POST['username_fld']);
		if(!preg_match("/^[a-zA-Z0-9]*$/",$username))
		{
			$username_err = "Only alphanumeric characters allowed.";
		}
	}
	
	// check if blank password was submitted. Blank password has a valid hash.
	// we could do this with javascript too but this is an easy workaround for the time being.
	if($_POST['hashed'] == 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855')
	{
		$password_err = "Password is required.";
	}else{
		$hashword = $_POST['hashed'];
		
		// run the username through database looking for a match
		$link = connect_db_read();
		if($stmt = mysqli_prepare($link, "SELECT id, hashword FROM `users` WHERE username = ? LIMIT 1"))
		{
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $uid, $db_hashword);
			
			if(!mysqli_stmt_fetch($stmt))
			{
				$username_err = "Username not recognized.";
			}else{
				// check the submitted hash against the stored hash
				if($db_hashword != $hashword)
				{
					$password_err = "Password Incorrect";
				}
			}
		}
		mysqli_close($link);
	}
}
if($_SERVER["REQUEST_METHOD"] != "POST" || !empty($username_err) || !empty($password_err))
{
?>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Scholastic Tracking and Reward System</title>
		<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="sha256.js"><noscript>Javascript required to log in securely</noscript></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#login_btn').click(function() {
					hash = Sha256.hash($('#password_fld').val());
					$('#hashed').val(hash);
					$('#password_fld').val("");
					//alert(hash);
				});
			});
		</script>
	</head>
	<body id="indexlogin">
		<div id="page_content">
			<div id="container">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
					<fieldset>
						<legend lang="en" dir="ltr">Log In</legend>
						<input type="text" name="username_fld" id="username_fld"><p class="field_error"><?php echo $username_err; ?></p><br>
						<input type="password" name="password_fld" id="password_fld"><p class="field_error"><?php echo $password_err ?></p><br>
						<input type="hidden" name="hashed" id="hashed">
						<input type="submit" id="login_btn" value="Log In"><br>
						<br>
						Don't have an account? <a href="create_account.php">Create One</a><br>
						<a href="account.php?q=forgot_username">Forgot Username?</a><br>
						<a href="account.php?q=forgot_password">Forgot Password?</a>
					</fieldset>
				</form>
			</div>
		</div>
<?php
}else{
	// Form submitted correctly and no errors so let's log the user in already.
	sec_session_start();
	if($hashword == $db_hashword)
	$_SESSION['logged'] = True;
	$_SESSION['uid'] = $uid;
	
	//while we have PHP still here grab the user's information to display.
	$link = connect_db_read();
	if($class_stmt = mysqli_prepare($link, "SELECT id, name, total_pts FROM `classes` WHERE student = ?"))
	{
		mysqli_stmt_bind_param($class_stmt, "i", $uid);
		mysqli_stmt_execute($class_stmt);
		mysqli_stmt_bind_result($class_stmt, $class_id, $class_name, $class_total_pts);
	}
	
	// display the dashboard page
?>
	<head>
		<meta charset="urf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Your Ivy-League Dashboard</title>
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
								}
							}
						}else
							echo 'something wrong';
					
						echo '					<div class="singleclass"><div class="class_name">'.$class_name.'</div><div class="wrapper"><div class="class_points">'.number_format($total_points_earned, 2)."/".number_format($class_max_points).'</div><div class="class_letter_grade">'.print_letter_grade($total_points_earned/$class_max_points)."</div></div></div>\n";
					}
					?>
				</div>
				<div id="point_summary">
					<h2>Point Summary</h2>
					<hr>
				</div>
			</div>
		</div>
<?php
}
?>
	</body>
</html>