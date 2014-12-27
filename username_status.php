<?php
include_once('common_functions.php');
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$db_link = connect_db_read();
	if($un_stmt = mysqli_prepare($db_link, "SELECT 1 FROM `users` WHERE username = ?"))
	{
		mysqli_stmt_bind_param($un_stmt, "s", $_POST['check']);
		mysqli_stmt_execute($un_stmt);
		mysqli_stmt_store_result($un_stmt);
		if(mysqli_stmt_num_rows($un_stmt) == 0)
			echo '<p class="field_good"> Username available</p>';
		elseif(mysqli_stmt_num_rows($un_stmt) >= 1)
			echo '<p class="field_error"> Username Taken</p>';
	}
}else
	echo '<p class="field_error"> Error: POST method not used</p>';