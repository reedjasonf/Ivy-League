<?php
include_once('common_functions.php');
sec_session_start();
if(!empty($_SESSION) && isset($_SESSION) && isset($_SESSION['logged'])) {
	if(ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
session_destroy();
?>
<!DOCTYPE>
<html>
	<head>
		<meta charset="urf-8">
		<meta http-equiv="refresh" content="5;URL=index.php"> 
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>You have been logged out...</title>
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
				<h2>You have been logged out...</h2>
				<h3>Click here if not returned to the homepage automatically</h3>
			</div>
		</div>
	</body>
</html>
<?php
}else{
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="urf-8">
		<meta http-equiv="refresh" content="5;URL=index.php"> 
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Unknown Request</title>
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
				<h2>We don't know what you were trying to do... but it didn't work.</h2>
				<h3>Click here if not returned to the homepage automatically</h3>
			</div>
		</div>
	</body>
</html>
<?php
}
?>