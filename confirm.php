<?php
include_once('common_functions.php');

if(COMPRESSION == TRUE){
	ob_start("ob_gzhandler");
}
sec_session_start();

//setup some variables
$action = array();
$action['result'] = null;

//quick/simple validation
if(empty($_GET['email']) || empty($_GET['key'])){
    $action['result'] = 'error';
    $action['text'] = 'Variables are missing. Please double check your email.';
}else{
	$link = connect_db_read();
	$email = mysqli_real_escape_string($link, urldecode($_GET['email']));
    $key = mysqli_real_escape_string($link, ($_GET['key']));
	
	// check if the confirm is in the database
	if($stmt = mysqli_prepare($link, 'SELECT * FROM `confirm` WHERE email=? AND codeword=? LIMIT 1'))
	{
		mysqli_stmt_bind_param($stmt, "ss", $email, $key);
		if(mysqli_stmt_execute($stmt))
		{
			mysqli_stmt_store_result($stmt);
			if(mysqli_stmt_num_rows($stmt) == 1)
			{
				mysqli_stmt_bind_result($stmt, $cid, $cEmail, $cKey, $cDate, $cUserID);
				mysqli_stmt_fetch($stmt);
				mysqli_stmt_close($stmt);
				mysqli_close($link);
				// Update the user, they confirmed their email address.
				$link = connect_db_update();
				if($stmt = mysqli_prepare($link, "UPDATE users SET active = 1 WHERE id = ? LIMIT 1"))
				{
					mysqli_stmt_bind_param($stmt, "i", $cUserID);
					if(mysqli_stmt_execute($stmt))
					{
						mysqli_stmt_close($stmt);
						mysqli_close($link);
						
						// attempt to delete the row from the confirm table. (Best effort)
						$link = connect_db_delete();
						$stmt = mysqli_prepare($link, "DELETE FROM confirm WHERE id = ? LIMIT 1") or die(mysqli_error($link));
						mysqli_stmt_bind_param($stmt, "i", $cid);
						mysqli_stmt_execute($stmt);
						
						$action['result'] = 'success';
						$action['text'] = 'Email address confirmed successfully.';
					}
				}else{
					$action['result'] = 'error';
					$action['text'] = 'Update error. The email address was confirmed BUT the database could not be updated. Reason: '.mysqli_error($link);
				}
			}else{
				if(mysqli_stmt_num_rows($stmt) > 1)
				{
					$action['result'] = 'error';
					$action['text'] = 'Too many results returned.';
				}else{
					$action['result'] = 'error';
					$action['text'] = mysqli_stmt_num_rows($stmt).'Confirmation Error. No results in the database using provided email and key. Check URL provided in email.';
				}
			}
			
		}else{
			$action['result'] = 'error';
			$action['text'] = 'Execution Error. Database could not be read. Reason: '.mysqli_error($link);
		}
	}else{
		$action['result'] = 'error';
		$action['text'] = 'Could not check database. Try again later. If problem persists contact support. Reason: '.mysqli_error($link);
	}
}

// output the page and the appropriate error/success messages

echo '<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="urf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Ivy-League Email Confirmation</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script> 
		$(document).ready(function(){
			$(".disappear").delay(2500).slideUp(1200, "swing");
		});
		</script> 
	</head>
	<body>
		<div id="page_content">
			<div id="banner">
				<h1>Ivy-League</h1>
				<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">';
				print_navbar_items();
echo '			</div>'."\n";
if(isset($action))
	switch($action['result'])
	{
		case "success":
			echo '			<div id="errorMessage" style="background: #99ff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
		break;
		
		case "unknown":
			echo '			<div id="errorMessage" style="background: #ffff66; color: black; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
		break;
		
		case "error":
			echo '			<div id="errorMessage" style="background: #ff5050; font-size: 1.75em; text-indent: 50px;">'.$action['text'].'</div>'."\n";
		break;
	}
echo '			<div id="container" style="min-height:75%">
				<div class="wrapper">
					<p>Return to the <a href="index.php">homepage</a></p>
				</div>
			</div>
		</div>
	</body>
</html>';
?>