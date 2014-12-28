<?php
include_once('common_functions.php');
sec_session_start();
if(COMPRESSION == TRUE){
	ob_start("ob_gzhandler");
}
include_once('passwordhash.php');

$username_err = $password_err = $name_err = $email_err = $privacy_err = $terms_err = $captcha_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// preprocess the fields for user errors and hacking attempts
	
	// check the username is appropriate and matches the length/restrictions requirements
	if(empty($_POST['username_fld']))
		$username_err = "Username is required.";
	else{
		$username = test_input($_POST['username_fld']);
		if(!preg_match("/^[a-zA-Z][a-zA-Z0-9-_]{5,35}$/", $username))
		{
			$username_err = "Username must start with A-Z and only contain letters, numbers, hyphen, and underscore and be between 6 and 36 characters.";
		}else{
			if(userexists($username))
				$username_err = "Username already taken.";
		}
	}
	
	// check if password is blank (hashed) Blank password has a "valid" hash
	if($_POST['hashed_pri'] == 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855')
		$password_err = "Password must not be blank";
	elseif(!slow_equals($_POST['hashed_pri'], $_POST['hashed_sec']))
		$password_err = "Passwords do not match";
	else{
		$hashword = $_POST['hashed_pri'];
	}
	
	if(empty($_POST['firstname_fld']))
		$name_err = "First name is required.";
	else{
		$firstname = test_input($_POST['firstname_fld']);
		$firstname = strtolower($firstname);
		$firstname = ucfirst($firstname);
	}
	
	if(empty($_POST['firstname_fld']))
		$name_err = "Last name is required.";
	else{
		$lastname = test_input($_POST['lastname_fld']);
		$lastname = strtolower($lastname);
		$lastname = ucfirst($lastname);
	}
	
	if(empty($_POST['email_fld']))
		$email_err = 'An email address is required.';
	else{
		$email = test_input($_POST['email_fld']);
	}
	
	if(empty($_POST['privacy']))
		$privacy_err = 'The privacy policy must be accepted.';
	if(empty($_POST['terms']))
		$terms_err = 'The Terms of Service must be accepted.';
		
	if(empty($_POST['captchaGuessFld']))
		$captcha_err = 'You must authenticate yourself as human.';
	else{
		$captcha_guess = test_input($_POST['captchaGuessFld']);
		$captcha_guess = hash("sha256", strtolower($captcha_guess));
		if($captcha_guess != $_SESSION['recaptcha'])
		{
			$captcha_err = 'String does not match. Try again!';
		}
	}
	
	// if no errors, submit data to database
	if(m_empty($email_err, $password_err, $name_err, $username_err, $privacy_err, $terms_err, $captcha_err))
	{
		// create a salt and create the hash string for the db
		$hash = create_hash($hashword);
		$dblink = connect_db_insert();		
		$stmt = mysqli_prepare($dblink, "INSERT INTO `users` (`id`, `username`, `hashword`, `org`, `team`, `first_name`, `last_name`, `email`) VALUES (NULL, ?, ?, NULL, NULL, ?, ?, ?)") or die(mysqli_error($dblink));
		mysqli_stmt_bind_param($stmt, "sssss", $username, $hash, $firstname, $lastname, $email);
		mysqli_stmt_execute($stmt) or die(mysqli_error($dblink));
		mysqli_close($dblink);
?>
<!DOCTYPE>
<html>
	<head>
		<meta charset="urf-8">
		<meta http-equiv="refresh" content="4;URL=index.php"> 
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Account created!</title>
	</head>
	<body id="logout">
		<div id="page_content">
			<div id="banner">
			<h1>Ivy-League STS</h1>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container">
				<h2>Account has been created successfully!</h2>
				<h3>Click <a href="index.php" lang="en" dir="ltr">here</a> if not returned to the login page automatically</h3>
			</div>
		</div>
	</body>
</html>
<?php
	}else
		goto error; // but if errors exist go back to the form and show errors on the form
}else{
error:

?>
<!DOCTYPE html>
<html lang='en' dir='ltr'>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Create an Ivy-League Account</title>
		<script type="text/javascript" src="sha256.js"><noscript>Javascript required to log in securely</noscript></script>
		<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="checkusername.ajax"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#create_btn').click(function() {
					hash1 = Sha256.hash($('#password_pri_fld').val());
					hash2 = Sha256.hash($('#password_sec_fld').val());
					$('#hashed_pri').val(hash1);
					$('#hashed_sec').val(hash2);
					$('#password_pri_fld').removeAttr('required');
					$('#password_pri_fld').val("");
					$('#password_sec_fld').removeAttr('required');
					$('#password_sec_fld').val("");
				});
			});
		</script>
	</head>
	<body id="create_account_form">
		<div id="page_content">
			<div id="banner">
				<h1>Ivy-League STS</h1>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
					<fieldset>
						<legend lang="en" dir="ltr">Account Information</legend>
						<label for="username_fld">Username: *</label><input type="text" name="username_fld" id="username_fld" autocomplete="off" onkeyup="UserExists(this.value)" pattern="^[a-zA-Z][a-zA-Z0-9-_]{5,36}$" required><p class="field_error" id="username_error"><?php echo $username_err; ?></p><br>
						<label for="password_pri_fld">Password: *</label><input type="password" name="password_pri_fld" id="password_pri_fld" required><p class="field_error"><?php echo $password_err; ?></p><br>
						<label for="password_sec_fld">Re-type Password: *</label><input type="password" name="password_sec_fld" id="password_sec_fld" required><br>
						<input type="hidden" name="hashed_pri" id="hashed_pri">
						<input type="hidden" name="hashed_sec" id="hashed_sec">
					</fieldset>
					<fieldset>
						<legend lang="en" dir="ltr">Contact Information</legend>
						<label for="firstname_fld">First Name: *</label><input type="text" name="firstname_fld" id="firstname_fld" required> 
						<label for="lastname_fld">Last Name: *</label><input type="text" name="lastname_fld" id="lastname_fld" required><p class="field_error"><?php echo $name_err; ?></p><br>
						<label for="email_fld">E-mail: *</label><input type="email" name="email_fld" id="email_fld" size="48" required><br>
					</fieldset>
					<fieldset>
						<legend lang="en" dir="ltr">Legal Terms & Verification</legend>
						<label for="privacy">I have read and accept the terms of the <a href="privacy_policy.html" target="_blank">privacy policy</a>*</label><input type="checkbox" name="privacy" id="privacy" required><p class="field_error"><?php echo $privacy_err; ?></p><br>
						<label for="terms">I have read and accept the <a href="TermsofService.html" target="_blank">Terms of Service.</a>*</label><input type="checkbox" name="terms" id="terms" required><p class="field_error"><?php echo $terms_err; ?></p><br>
						<image src="captcha_image.php" alt="Captcha image" style="margin-left:2em"><br>
						<label for="captchaGuessFld">Type the characters to prove you are human: *</label><input type="text" name="captchaGuessFld" id="captchaGuessFld" autocomplete="off" required><p class="field_error"><?php echo $captcha_err; ?></p><br>
					</fieldset>
					<br>
					<input type="submit" value="Create Account" id="create_btn">
				</form>
			</div>
		</div>
	</body>
</html>
<?php
}
?>