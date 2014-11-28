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
	$_SESSION['username'] = $username;
	$_SESSION['login_string'] = hash('sha512', $hashword . $_SERVER['HTTP_USER_AGENT']);
	
	//send the user to the dashboard page
	header("Location: dashboard.php");
}
?>