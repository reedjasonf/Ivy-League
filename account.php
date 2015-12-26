<?php
include_once('common_functions.php');
include_once('passwordhash.php');

if(COMPRESSION == TRUE){
	ob_start("ob_gzhandler");
}
sec_session_start();

echo '<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="urf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<script type="text/javascript" src="sha256.js"><noscript>Javascript required to secure your password</noscript></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script> 
		$(document).ready(function(){
			$(".disappear").delay(2500).slideUp(1200, "swing");
			
			$(\'#reload_captcha\').click(function() {
					$("#captcha_image").attr("src", "captcha_image.php?"+(new Date()).getTime());
					return false;
				});
			$(\'#create_btn\').click(function() {
					hash1 = Sha256.hash($(\'#password\').val());
					hash2 = Sha256.hash($(\'#passwordConfirm\').val());
					$(\'#hashed_pri\').val(hash1);
					$(\'#hashed_sec\').val(hash2);
					$(\'#password\').removeAttr(\'required\');
					$(\'#password\').val("");
					$(\'#passwordConfirm\').removeAttr(\'required\');
					$(\'#passwordConfirm\').val("");
				});
		});
		</script>'."\n";

if(isset($_GET['function']))
	switch($_GET['function'])
	{
		case "forgot_password":
			// User will need to provide their username and registered email address.
			// If the information matches with the database... send an email to the
			// email in the database... if they verified it.
			
			// email address will contain a link containing their username, email and a secret key
			// the codeword should only be available for 15 minutes then it is expired
			
			// if the codeword matches present a form that will allow them to change their password
			$action = array();
			$action['result'] = null;
			
			if(empty($_GET['username']) || empty($_GET['email']) || empty($_GET['key']))
			{
				// present the form to start the process that will send the email
				// or a variable may be missing...
				if($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					if(empty($_POST['username']))
					{
						$action['result'] = 'error';
						$action['text'] = 'The username field is required and can not be blank.';
					}else{
						if(empty($_POST['email']))
						{
							$action['result'] = 'error';
							$action['text'] = 'The email field is required and can not be blank.';
						}else{
							if(empty($_POST['captchaGuessFld']))
							{
								$action['result'] = 'error';
								$action['text'] = 'The captcha field is required and can not be blank.';
							}else{
								$captcha_guess = test_input($_POST['captchaGuessFld']);
								$captcha_guess = hash("sha256", strtolower($captcha_guess));
								if($captcha_guess != $_SESSION['recaptcha'])
								{
									$action['result'] = 'error';
									$action['text'] = 'Captcha string does not match. Try again!';
								}else{
									// everything with the form is in order...
									// clean the input
									$link = connect_db_read();
									$username = mysqli_real_escape_string($link, test_input($_POST['username']));
									$email = mysqli_real_escape_string($link, test_input($_POST['email']));
									
									// check the database for a row matching the input
									if($stmt = mysqli_prepare($link, "SELECT id, email, username FROM users WHERE email = ? AND username = ? AND active = 1 LIMIT 1"))
									{
										mysqli_stmt_bind_param($stmt, "ss", $email, $username);
										if(mysqli_stmt_execute($stmt))
										{
											mysqli_stmt_store_result($stmt);
											if(mysqli_stmt_num_rows($stmt) == 1)
											{
												// exactly one row was returned. Use it to send the email.
												mysqli_stmt_bind_result($stmt, $uid, $dbEmail, $dbUsername);
												mysqli_stmt_fetch($stmt);
												mysqli_stmt_close($stmt);
												
												// Before sending the email put a row in a table that we will check against when they click the link
												$timestamp = time();
												$key = md5($dbUsername.$dbEmail.$timestamp);
												
												$link = connect_db_insert();
												if($stmt = mysqli_prepare($link, "INSERT INTO `confirm` (`email`, `codeword`, `date`, `userid`, `type`) VALUES (?, ?, ?, ?, 'password')"))
												{
													mysqli_stmt_bind_param($stmt, "sssi", $dbEmail, $key, date('Y-m-d H:i:s', $timestamp), $uid);
													if(mysqli_stmt_execute($stmt))
													{
														if(mysqli_stmt_affected_rows($stmt) == 1)
														{
															// send the email
															$subject = 'Ivy League Password Reset';
															$headers  = 'MIME-Version: 1.0' . "\r\n";
															$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
															$headers .= "From: no.reply.ivyleague@gmail.com\r\n";
															$message = '<p>Hello, </p>
<p>A request to reset your password was received. In order to keep your account secure your password can only be reset by clicking on this <a href="http://localhost/Ivy-League/account.php?function=forgot_password&email='.urlencode($dbEmail).'&username='.urlencode($dbUsername).'&key='.$key.'">link</a>. If your browser does not display the link correctly copy and past the following URL into the address bar of your browser: http://localhost/Ivy-League/account.php?function=forgot_password&email='.urlencode($dbEmail).'&username='.urlencode($dbUsername).'&key='.$key.'</p>
<p>Please be suspicious of emails that may attempt to mimic this one. If you did not request the password change, visit your Ivy League account and change the password manually to prevent unauthorized access.</p>
<p>Please do not reply to this email. This email is automatically generated from an unmonitored email address. If you need support please email support.</p>
<p>Sincerely,</p>
<p>The Ivy League Team</p>';
															if(mail($dbEmail, $subject, $message, $headers))
															{
																$action['result'] = 'success';
																$action['text'] = 'Password reset email sent. Please check your email to complete the process.';
															}else{
																$action['result'] = 'error';
																$action['text'] = 'Password reset email failed to send.';
															}
														}else{
															$action['result'] = 'error';
															$action['text'] = 'Insert error. Row failed to insert. Reason: '.mysqli_error($link);
														}
													}else{
														$action['result'] = 'error';
														$action['text'] = 'Insert execution error. Account found but lock not saved correctly. Reason: '.mysqli_error($link);
													}
												}else{
													$action['result'] = 'error';
													$action['text'] = 'Insert statement error. Account found but lock not saved correctly. Reason: '.mysqli_error($link);
												}
											}else{
												if(mysqli_stmt_num_rows($stmt) >1)
												{
													$action['result'] = 'error';
													$action['text'] = 'Illegal Result. Too many results returned.';
												}else{
													$action['result'] = 'error';
													$action['text'] = 'Zero results returned. Either email or username may be incorrect.';
												}
											}
										}else{
											$action['result'] = 'error';
											$action['text'] = 'Execution error. Query could not be run. Reason: '.mysqli_error($link);
										}
									}else{
										$action['result'] = 'error';
										$action['text'] = 'Statement Error. Query could not be run. Reason: '.mysqli_error($link);
									}
									mysqli_close($link);
								}
							}
						}
					}
				}
				echo '		<title>Password Recovery Form</title>
	</head>
	<body id="forgot_password">
		<div id="page_content">
			<div class="row">
				<div id="banner" class="col-12 col-m-12">
					<h1>Ivy-League</h1>
					<h3>Scholarship Tracking System</h3>
				</div>
			</div>
			<div class="row">
				<div id="navbar">'."\n";
				print_navbar_items();
			echo "\n".'				</div>
			</div>'."\n";
			if(isset($action))
				switch($action['result'])
				{
					case "success":
						echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
					break;
					
					case "unknown":
						echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
					break;
					
					case "error":
						echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
					break;
				}
			echo'			<div id="container" style="min-height:75%">
				<div class="row">
					<div class="col-2"></div>
					<div class="col-8 col-m-12">
						<form method="POST" action="">
							<fieldset>
								<label for="username">Username: *</label><input type="text" name="username" id="username" required><br>
								<label for="email">Registered Email: *</label><input type="email" name="email" id="email" required><br>
								<br>
								<img src="captcha_image.php" alt="Captcha image" id="captcha_image" style="margin-left:2em"><br>
								<a href="#" id="reload_captcha" name="reload_captcha">Load New Image</a><br><br>
								<label for="captchaGuessFld">Type the characters to prove you are human: *</label><input type="text" name="captchaGuessFld" id="captchaGuessFld" autocomplete="off" required>
								<p>If your email and username are located you will receive an email with a password reset link.</p>
								<input type="submit" value="Send Password Reset Instructions">
							</fieldset>
						</form>
					</div>
					<div class="col-2"></div>
				</div>
			</div>'."\n";
			}else{
				// this is where the user is sent when they click the link in the email
				// i.e. username, key, and email values are set in the URL
				if($_SERVER['REQUEST_METHOD'] == 'POST')
				{	
					// user has submitted the form to reset their password
					$action = array();
					$action['result'] = null;
					// reset the password or show an error
					// check if password is blank (hashed) Blank password has a "valid" hash
					if($_POST['hashed_pri'] == 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855')
					{
						$action['result'] = 'error';
						$action['text'] = "Password must not be blank";
					}
					elseif(!slow_equals($_POST['hashed_pri'], $_POST['hashed_sec']))
					{
						$action['result'] = 'error';
						$action['text'] = "Passwords do not match";
					}
					else{
						$hashword = $_POST['hashed_pri'];
					}
					
					if($action['result'] == null)
					{
						// passwords matched and wasn't empty
						$link = connect_db_read();
						// make sure the key still matches and is within the 15 minute window.
						if($stmt = mysqli_prepare($link, "SELECT id, email, codeword, date, userid FROM confirm WHERE email = ? AND codeword = ? LIMIT 1"))
						{
							mysqli_stmt_bind_param($stmt, "ss", urldecode($_GET['email']), urldecode($_GET['key']));
							if(mysqli_stmt_execute($stmt))
							{
								mysqli_stmt_store_result($stmt);
								if(mysqli_stmt_num_rows($stmt) == 1)
								{
									mysqli_stmt_bind_result($stmt, $cid, $cEmail, $cKey, $cDate, $cUserID);
									mysqli_stmt_fetch($stmt);
									if(time() > strtotime($cDate)+15*60)
									{
										$action['result'] = 'error';
										$action['text'] = 'The key has expired. You will need to request another password reset email.';
									}else{
										// key is still good so we can change the password now
										$hash = create_hash($hashword);
										$dblink = connect_db_update();
										if($stmt = mysqli_prepare($dblink, "UPDATE `users` SET hashword = ? WHERE id = ?"))
										{
											mysqli_stmt_bind_param($stmt, "si", mysqli_real_escape_string($dblink, $hash), $_POST['uid']);
											if(mysqli_stmt_execute($stmt))
											{
												$action['result'] = 'success';
												$action['text'] = 'Password changed successfully. Please log in again using the new password.';
											}else{
												$action['result'] = 'error';
												$action['text'] = 'Execution Error. Password was not changed. Reason: '.mysqli_error($dblink);
											}
										}else{
											$action['result'] = 'error';
											$action['text'] = 'Statement error. Password could not be changed. Reason: '.mysqli_error($dblink);
										}
									}
								}else{
									$action['result'] = 'error';
									$action['text'] = 'The key no longer exists.';
								}
							}else{
								$action['result'] = 'error';
								$action['text'] = 'Execution error. The key could not be validated. Reason: '.mysqli_error($link);
							}
						}else{
							$action['result'] = 'error';
							$action['text'] = 'Statement error. Statement could not be prepared. Reason: '.mysqli_error($link);
						}
						// display either a success or error message about the database
						echo '		<title>Password Recovery Form</title>
	</head>
	<body id="forgot_password">
		<div id="page_content">
			<div class="row">
				<div id="banner" class="col-12 col-m-12">
					<h1>Ivy-League</h1>
					<h3>Scholarship Tracking System</h3>
				</div>
			</div>
			<div class="row">
				<div id="navbar">'."\n";
				print_navbar_items();
						echo "\n".'				</div>
			</div>'."\n";
						if(isset($action))
							switch($action['result'])
							{
								case "success":
									echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
								break;
								
								case "unknown":
									echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
								break;
								
								case "error":
									echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
								break;
							}
						echo'			<div id="container" style="min-height:75%">
			</div>'."\n";
						
					}else{
						// display the error about the password
						echo '		<title>Password Recovery Form</title>
	</head>
	<body id="forgot_password">
		<div id="page_content">
			<div class="row">
				<div id="banner" class="col-12 col-m-12">
					<h1>Ivy-League</h1>
					<h3>Scholarship Tracking System</h3>
				</div>
			</div>
			<div class="row">
				<div id="navbar">'."\n";
				print_navbar_items();
						echo "\n".'				</div>
			</div>'."\n";
						if(isset($action))
							switch($action['result'])
							{
								case "success":
									echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
								break;
								
								case "unknown":
									echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
								break;
								
								case "error":
									echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
								break;
							}
						echo'			<div id="container" style="min-height:75%">
			</div>'."\n";
					}
				}else{
					// user has not submitted the form to change their password yet. Show them the form with two password fields on it.
					$link = connect_db_read();
					
					$username = mysqli_real_escape_string($link, urldecode($_GET['username']));
					$email = mysqli_real_escape_string($link, urldecode($_GET['email']));
					$key = mysqli_real_escape_string($link, urldecode($_GET['key']));
					
					// check the database to make sure the codeword is still valid. If not, delete it.
					
					if($stmt = mysqli_prepare($link, "SELECT id, email, codeword, date, userid FROM confirm WHERE email = ? AND codeword = ? LIMIT 1"))
					{
						mysqli_stmt_bind_param($stmt, "ss", $email, $key);
						if(mysqli_stmt_execute($stmt))
						{
							mysqli_stmt_store_result($stmt);
							if(mysqli_stmt_num_rows($stmt) == 1)
							{
								mysqli_stmt_bind_result($stmt, $cid, $cEmail, $cKey, $cDate, $cUserID);
								mysqli_stmt_fetch($stmt);
								if(time() > strtotime($cDate)+15*60)
								{
									$action['result'] = 'error';
									$action['text'] = 'The key has expired. You will need to request another password reset email.';
								}else{
									// the key is good. Show the form.
									echo '		<title>Password Recovery Form</title>
	</head>
	<body id="forgot_password">
		<div id="page_content">
			<div class="row">
				<div id="banner" class="col-12 col-m-12">
					<h1>Ivy-League</h1>
					<h3>Scholarship Tracking System</h3>
				</div>
			</div>
			<div class="row">
				<div id="navbar">'."\n";
					print_navbar_items();
									echo "\n".'				</div>
				</div>'."\n";
									if(isset($action))
										switch($action['result'])
										{
											case "success":
												echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
											break;
											
											case "unknown":
												echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
											break;
											
											case "error":
												echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
											break;
										}
									echo'			<div id="container" style="min-height:75%">
					<div class="row">
						<div class="col-2"></div>
						<div class="col-8 col-m-12">
							<form method="POST" action="">
								<fieldset>
									<p>You have been authenticated as the owner of this account. Please select a new password and submit the form. After changing your password you will need to log in again.</p>
									<label for="password">Password: *</label><input type="password" name="password" id="password" required><br>
									<label for="confirm">Confirm: *</label><input type="password" name="passwordConfirm" id="passwordConfirm" required><br>
									<input type="hidden" name="hashed_pri" id="hashed_pri">
									<input type="hidden" name="hashed_sec" id="hashed_sec">
									<input type="hidden" name="uid" id="uid" value="'.$cUserID.'">
									<br>
									<input type="submit" value="Reset Password" id="create_btn">
								</fieldset>
							</form>
						</div>
						<div class="col-2"></div>
					</div>
				</div>'."\n";
								}
							}else{
								if(mysqli_stmt_num_rows($stmt) > 1)
								{
									$action['result'] = 'error';
									$action['text'] = 'Too many results returned.';
								}else{
									$action['result'] = 'error';
									$action['text'] = 'Confirmation Error. No results in the database using provided email and key. Check URL provided in email.';
								}
							}
						}else{
							$action['result'] = 'error';
							$action['text'] = 'Execution error. Could not verify key against database. Reason: '.mysqli_error($link);
						}
						mysqli_stmt_close($stmt);
					}
					mysqli_close($link);
					if($action['result'] == 'error')
					{
						// show a page but only for the error message
						echo '		<title>Password Recovery Form</title>
		</head>
		<body id="forgot_password">
			<div id="page_content">
				<div class="row">
					<div id="banner" class="col-12 col-m-12">
						<h1>Ivy-League</h1>
						<h3>Scholarship Tracking System</h3>
					</div>
				</div>
				<div class="row">
					<div id="navbar">'."\n";
					print_navbar_items();
									echo "\n".'				</div>
					</div>'."\n";
									if(isset($action))
										switch($action['result'])
										{
											case "success":
												echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
											break;
											
											case "unknown":
												echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
											break;
											
											case "error":
												echo '			<div class="col-12 col-m-12" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
											break;
										}
									echo'			<div id="container" style="min-height:75%">
				</div>'."\n";
					}
				}
			}
		break;
		// end lost password case
		
		case "forgot_username":
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				// form has been submitted and needs to be processed
				$action = array();
				$action['result'] = null;
				
				$link = connect_db_read();
				if(empty($_POST['email']))
				{
					$action['result'] = 'error';
					$action['text'] = 'Email field is required and can not be blank.';
				}else{
					if(empty($_POST['captchaGuessFld']))
					{
						$action['result'] = 'error';
						$action['text'] = 'Captcha field is required and can not be blank.';
					}else{
						$captcha_guess = test_input($_POST['captchaGuessFld']);
						$captcha_guess = hash("sha256", strtolower($captcha_guess));
						if($captcha_guess == $_SESSION['recaptcha'])
						{
							if($stmt = mysqli_prepare($link, "SELECT username, email FROM users WHERE email = ? AND active = 1 LIMIT 1"))
							{
								mysqli_stmt_bind_param($stmt, "s", mysqli_real_escape_string($link, $_POST['email']));
								if(mysqli_stmt_execute($stmt))
								{
									mysqli_stmt_store_result($stmt);
									if(mysqli_stmt_num_rows($stmt) == 1)
									{
										mysqli_stmt_bind_result($stmt, $username, $email);
										mysqli_stmt_fetch($stmt);
										mysqli_stmt_close($stmt);
										mysqli_close($link);
										
										// send the email
										$subject = 'Ivy League Username';
										$headers  = 'MIME-Version: 1.0' . "\r\n";
										$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
										$headers .= "From: no.reply.ivyleague@gmail.com\r\n";
										$message = '<p>Hello, </p>
<p>Someone requested your username with the Ivy League Scholarship Tracking System. Your username is: '.$username.'. If this was not requested by you please make sure you have a strong password set.</p>
<p>Please do not reply to this email. This email is automatically generated from an unmonitored email address. If you need support please email support.</p>
<p>Sincerely,</p>
<p>The Ivy League Team</p>';
										if(mail($email, $subject, $message, $headers))
										{
											$action['result'] = 'success';
											$action['text'] = 'Recovery email sent successfully.';
										}else{
											$action['result'] = 'error';
											$action['text'] = 'Email not able to be sent.';
										}
									}else{
										if(mysqli_stmt_num_rows($stmt) > 1)
										{
											$action['result'] = 'error';
											$action['text'] = 'Too many results returned.';
										}else{
											$action['result'] = 'error';
											$action['text'] = 'No results returned. Email not in database.';
										}
									}
								}else{
									$action['result'] = 'error';
									$action['text'] = 'Execution error. Reason: '.mysqli_error($link);
								}
							}else{
								$action['result'] = 'error';
								$action['text'] = 'Statement error. Query could not be run. Reason: '.mysqli_error($link);
							}
						}else{
							$action['result'] = 'error';
							$action['text'] = 'Captcha error. Text did not match the image.';
						}
					}
				}
			}
				//Output the HTML form.
			echo '		<title>Username Recovery Form</title>
	</head>
	<body id="forgot_username">
		<div id="page_content">
			<div class="row">
				<div id="banner" class="col-12 col-m-12">
					<h1>Ivy-League</h1>
					<h3>Scholarship Tracking System</h3>
				</div>
			</div>
			<div class="row">
				<div id="navbar">'."\n";
				print_navbar_items();
			echo "\n".'				</div>
			</div>'."\n";
			if(isset($action))
				switch($action['result'])
				{
					case "success":
						echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
					break;
					
					case "unknown":
						echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
					break;
					
					case "error":
						echo '			<div class="disappear col-12 col-m-12" id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
					break;
				}
			echo'			<div id="container" style="min-height:75%">
				<div class="row">
					<div class="col-2"></div>
					<div class="col-8 col-m-12">
						<form method="POST" action="">
							<fieldset>
								<label for="email">Registered Email: </label><input type="email" name="email" id="email" required><br><br>
								<img src="captcha_image.php" alt="Captcha image" id="captcha_image" style="margin-left:2em"><br>
								<a href="#" id="reload_captcha" name="reload_captcha">Load New Image</a><br><br>
								<label for="captchaGuessFld">Type the characters to prove you are human: *</label><input type="text" name="captchaGuessFld" id="captchaGuessFld" autocomplete="off" required>
								<p>If your email is detected an email will be sent to your inbox with your username.</p>
								<input type="submit" value="Send Username">
							</fieldset>
						</form>
					</div>
					<div class="col-2"></div>
				</div>
			</div>'."\n";
		break;
	}
echo '		</div>
	</body>
</html>';

?>