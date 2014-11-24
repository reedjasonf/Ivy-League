<?php
include('common_functions.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){
	// process username, and password fields. Password should be empty and "hashed" should match db password.
	$username = test_input($_POST['username'])
	$link = connect_db_read();
	if ($result = mysqli_query($link, "SELECT * FROM `users` WHERE username"))
}else{
	echo "FATAL SECURITY ERROR!: Form not submitted. Log Entry Created.";
	$entry = "Direct visit to ".$_SERVER['PHP_SELF']." from IP ".$_SERVER['REMOTE_ADDR'].". [".date_with_micro('Y-m-d H:i:s:u')."]\n";
	file_put_contents("logs/security.txt", $entry, FILE_APPEND | LOCK_EX);
}
?>