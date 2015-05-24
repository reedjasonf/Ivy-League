<?php
include_once ('../../scholarbowl_config.php');

function connect_db_read(){
	$link = mysqli_connect(HOST,READ_USER,READ_PASSWORD,DATABASE) or die("Error " . mysqli_error($link));
	if(mysqli_connect_errno()) {
		printf("Connect to database failed: %s\n", mysqli_connect_error());
		return false;
	}else
		return $link;
}

function connect_db_insert(){
	$link = mysqli_connect(HOST,INSERT_USER,INSERT_PASSWORD,DATABASE) or die("Error " . mysqli_error($link));
	if(mysqli_connect_errno()) {
		printf("Connect to database failed: %s\n", mysqli_connect_error());
		return false;
	}else
		return $link;
}

function m_empty()
{
    foreach(func_get_args() as $arg)
        if(empty($arg))
            continue;
        else
            return false;
    return true;
}

function userexists($un)
{
	$db_link = connect_db_read();
	if($un_stmt = mysqli_prepare($db_link, "SELECT 1 FROM `users` WHERE username = ?"))
	{
		mysqli_stmt_bind_param($un_stmt, "s", $un);
		mysqli_stmt_execute($un_stmt);
		mysqli_stmt_store_result($un_stmt);
		if(mysqli_stmt_num_rows($un_stmt) == 0)
			return false;
		elseif(mysqli_stmt_num_rows($un_stmt) >= 1)
			return true;
	}else
		return NULL;
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

function login_check() {
	$mysqli = connect_db_read();
    // Check if all session variables are set 
    if (isset($_SESSION['uid'], $_SESSION['logged'], $_SESSION['username'], $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['uid'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT hashword FROM users WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
				
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}

function print_navbar_items() {
	$loggedIn = login_check();
	echo '<p class="'; echo $_SERVER['PHP_SELF'] == '/scholarbowl/Ivy-League/dashboard.php' ? 'navcurrent' : 'navlink'; echo '"><a href="';echo $loggedIn==True ? 'dashboard.php' : 'index.php'; echo '">Home</a></p>
				<p class="navlink">About</p>';
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
	elseif($percentage == -1)
		return "";
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

function get_user_classes($user_id, $archived_classes = 0) {
	$myArray = [];
	$link = connect_db_read();
	if($class_stmt = mysqli_prepare($link, "SELECT id FROM `classes` WHERE student = ? AND archived = ".$archived_classes))
	{
		mysqli_stmt_bind_param($class_stmt, "i", $user_id);
		mysqli_stmt_execute($class_stmt);
		mysqli_stmt_bind_result($class_stmt, $class_id);
		while(mysqli_stmt_fetch($class_stmt))
		{
			$myArray[] = $class_id;
		}
	}else
		$myArray = False;
	mysqli_close($link);
	return $myArray;
}

function get_class_name($class_id) {
	$link = connect_db_read();
	if($class_stmt = mysqli_prepare($link, "SELECT name FROM `classes` WHERE id = ?"))
	{
		mysqli_stmt_bind_param($class_stmt, "i", $class_id);
		mysqli_stmt_execute($class_stmt);
		mysqli_stmt_bind_result($class_stmt, $class_name);
		mysqli_stmt_fetch($class_stmt);
		$var = $class_name;
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function get_class_max_points($class_id) {
	$link = connect_db_read();
	if($class_stmt = mysqli_prepare($link, "SELECT total_pts FROM `classes` WHERE id = ?"))
	{
		mysqli_stmt_bind_param($class_stmt, "i", $class_id);
		mysqli_stmt_execute($class_stmt);
		mysqli_stmt_bind_result($class_stmt, $class_max_points);
		mysqli_stmt_fetch($class_stmt);
		$var = $class_max_points;
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function class_categories_names($class_id) {
	$myArray = [];
	$link = connect_db_read();
		if($cat_stmt = mysqli_prepare($link, "SELECT id, name FROM `grade_categories` WHERE class = ?"))
		{
			mysqli_stmt_bind_param($cat_stmt, "i", $class_id);
			mysqli_stmt_execute($cat_stmt);
			mysqli_stmt_bind_result($cat_stmt, $cat_id, $cat_name);
			while(mysqli_stmt_fetch($cat_stmt))
			{
				$myArray[$cat_id] = $cat_name;
			}
		}else
			$myArray = False;
	mysqli_close($link);
	return $myArray;
}

function get_class_categories($class_id) {
	$myArray = [];
	$link = connect_db_read();
		if($cat_stmt = mysqli_prepare($link, "SELECT id FROM `grade_categories` WHERE class = ?"))
		{
			mysqli_stmt_bind_param($cat_stmt, "i", $class_id);
			mysqli_stmt_execute($cat_stmt);
			mysqli_stmt_bind_result($cat_stmt, $cat_id);
			while(mysqli_stmt_fetch($cat_stmt))
			{
				$myArray[] = $cat_id;
			}
		}else
			$myArray = False;
	mysqli_close($link);
	return $myArray;
}

function num_in_category($cat_id) {
	$link = connect_db_read();
	if($cat_stmt = mysqli_query($link, ("SELECT 1 FROM `grades` WHERE category = ".htmlspecialchars($cat_id))))
	{
		//mysqli_stmt_bind_param($cat_stmt, "i", $cat_id);
		//mysqli_query($cat_stmt);
		return mysqli_affected_rows($link);
	}else
		return False;
	mysqli_close($link);
}

function category_name($cat_id) {
	$link = connect_db_read();
	if($cat_stmt = mysqli_prepare($link, "SELECT name FROM `grade_categories` WHERE id = ?"))
	{
		mysqli_stmt_bind_param($cat_stmt, "i", $cat_id);
		mysqli_stmt_execute($cat_stmt);
		mysqli_stmt_bind_result($cat_stmt, $cat_name);
		mysqli_stmt_fetch($cat_stmt);
		$var = $cat_name;
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function category_pts_earned($cat_id) {
	$link = connect_db_read();
	if($stmt = mysqli_prepare($link, "SELECT SUM(points_earned) FROM `grades` WHERE category = ?"))
	{
		mysqli_stmt_bind_param($stmt, "i", $cat_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $points_earned);
		mysqli_stmt_fetch($stmt);
		$var = number_format($points_earned,2);
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function category_pts_offered($cat_id) {
	$link = connect_db_read();
	if($stmt = mysqli_prepare($link, "SELECT SUM(max_points) FROM `grades` WHERE category = ?"))
	{
		mysqli_stmt_bind_param($stmt, "i", $cat_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $points_offered);
		mysqli_stmt_fetch($stmt);
		$var = number_format($points_offered, 2);
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function category_max_points($cat_id) {
	$link = connect_db_read();
	if($cat_stmt = mysqli_prepare($link, "SELECT max_points FROM `grade_categories` WHERE id = ?"))
	{
		mysqli_stmt_bind_param($cat_stmt, "i", $cat_id);
		mysqli_stmt_execute($cat_stmt);
		mysqli_stmt_bind_result($cat_stmt, $cat_maxpts);
		mysqli_stmt_fetch($cat_stmt);
		$var = $cat_maxpts;
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function category_percent($cat_id) {
	$link = connect_db_read();
	$total_earned = $max = $max_available = 0;
	if($cat_stmt = mysqli_prepare($link, "SELECT grades.points_earned, grades.max_points, grade_categories.max_points FROM `grade_categories` INNER JOIN `grades` ON grades.category = grade_categories.id WHERE grades.category = ? "))
	{
		mysqli_stmt_bind_param($cat_stmt, "i", $cat_id);
		mysqli_stmt_execute($cat_stmt);
		mysqli_stmt_bind_result($cat_stmt, $pts_earned, $assignment_max, $cat_max_pts);
		$i = 1;
		while(mysqli_stmt_fetch($cat_stmt))
		{
			$total_earned += $pts_earned;
			$max_available += $assignment_max;
			if($i == 1){
				$max = $cat_max_pts;
				$i++;
			}
		}
		if($max_available != 0)
			$var = $total_earned/$max_available;
		else
			$var = 0;
	}else
		$var = False;
	mysqli_close($link);
	return $var;
}

function GPAfactor($percent) {
	if($percent >= 0.9 && $percent <= 1)
		$var = 4.0;
	elseif($percent >= 0.8 && $percent < 0.9)
		$var = 3.0;
	elseif($percent >= 0.7 && $percent < 0.8)
		$var = 2.0;
	elseif($percent >= 0.6 && $percent < 0.7)
		$var = 1.0;
	else
		$var = 0.0;
	return $var;
}

function fetch_assignment_percents($cat_id) {
	$myArray = [];
	$link = connect_db_read();
	if($grades_stmt = mysqli_prepare($link, "SELECT points_earned, max_points FROM `grades` WHERE category = ?"))
	{
		mysqli_stmt_bind_param($grades_stmt, "i", $cat_id);
		mysqli_stmt_execute($grades_stmt);
		mysqli_stmt_bind_result($grades_stmt, $pts_earned, $pts_available);
		while(mysqli_stmt_fetch($grades_stmt))
		{
			if($pts_available > 0)
				$myArray[] = $pts_earned/$pts_available;
		}
	}else
		$myArray = False;
	mysqli_close($link);
	return $myArray;
}
?>