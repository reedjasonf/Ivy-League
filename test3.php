<?php

include_once('class_def.php');

$myUser = new user(1);
echo $myUser->lname;
echo '<pre>';
print_r($myUser->courses);

?>