<?php
include_once ('../../scholarbowl_config.php');
function connect_db_read(){
	$link = mysqli_connect(HOST,USER,PASSWORD,DATABASE) or die("Error " . mysqli_error($link));
	if(mysqli_connect_errno()) {
		printf("Connect to database failed: %s\n", mysqli_connect_error());
		return false;
	}else
		return $link;
}

function sec_session_start() {
    $session_name = 'scholarbowl_sec_session';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        die("Could not initiate a safe session."); //header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id(TRUE);    // regenerated the session, delete the old one. 
}
function print_letter_grade($percentage) {
	if($percentage >= .975 && $percentage <= 1)
		return "A+";
	elseif($percentage >= .925 && $percentage < .975)
		return "A";
	elseif($percentage >= .90 && $percentage < .925)
		return "A-";
	elseif($percentage >= .875 && $percentage < .9)
		return "B+";
	elseif($percentage >= .825 && $percentage < .875)
		return "B";
	elseif($percentage >= .8 && $percentage < .825)
		return "B-";
	elseif($percentage >= .775 && $percentage < .8)
		return "C+";
	elseif($percentage >= .725 && $percentage < .775)
		return "C";
	elseif($percentage >= .7 && $percentage < .725)
		return "C-";
	elseif($percentage >= .675 && $percentage < .7)
		return "D+";
	elseif($percentage >= .625 && $percentage < .675)
		return "D";
	elseif($percentage >= .6 && $percentage < .625)
		return "D-";
	elseif($percentage >= .575 && $percentage < .6)
		return "F+";
	else
		return "F";
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

/**
* Quick replacement to date() function to handle the 'u' format specifier (for microseconds)
* @param string $format Date format string - the same format string you would pass to date() function
* @param float $timestamp [optional] Unix timestamp with microseconds - Typically output of <b>microtime(true)</b>
* @return string Formatted string
*/
function date_with_micro($format, $timestamp = null) {
	if (is_null($timestamp) || $timestamp === false) {
		$timestamp = microtime(true);
	}
	$timestamp_int = (int) floor($timestamp);
	$microseconds = (int) round(($timestamp - floor($timestamp)) * 1000000.0, 0);
	$format_with_micro = str_replace("u", $microseconds, $format);
	return date($format_with_micro, $timestamp_int);
} 

?>