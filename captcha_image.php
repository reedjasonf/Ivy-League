<?php
include_once('common_functions.php');
sec_session_start();
$alpha = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
$passphrase = "";
$length = rand(7,10);
for($i = 1; $i <= $length; $i++)
{
	$pos = rand(0,46);
	$sub = substr($alpha, $pos, 1);
	$passphrase .= $sub;
}

// set the password in a session variable for comparison later
$_SESSION['recaptcha'] = hash("sha256", strtolower($passphrase));

// now create the image
// include lines, and stuff to confuse bots but not people

$image = imagecreatefrompng("images/captcha.png");
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 80, 20, 60);

$slope = rand(13,16);
$period = rand(16, 25);


$posx = 0;
for($s=0; $s<strlen($passphrase); $s++)
{	
	$posx += rand(28,36);
	imagettftext($image, 32, rand(-25,30), $posx, rand(40,70), $white, 'ttf\Alido.otf', $passphrase[$s]);
}
// select a base color
$r = rand(5,250);
$g = rand(5,250);
$b = rand(5,250);
// creates horizontal squiggles
for($j=-5; $j < 8; $j++)
{
	// change the base color slightly to make a gradient
	$r = rand($r-20, $r+20);
	$g = rand($g-20, $g+20);
	$b = rand($b-20, $b+20);
	$base_color = imagecolorallocate($image, $r, $g, $b);
	$offset = rand(0,8);
	for($i=0; $i < 400; $i++)
	{
		$y = $j*26+($i/$slope)*cos(($i+$offset)/$period)+($i)/7;
		imagesetpixel($image, $i, $y, $base_color);
		imagesetpixel($image, $i, $y+1, $base_color);
		imagesetpixel($image, $i, $y+2, $base_color);
	}
		
	for($k=0; $k <120; $k++){
	
	}
}

// creates vertical squiggles
for($j=-3; $j < 20; $j++)
{
	$r = rand($r-20, $r+20);
	$g = rand($g-20, $g+20);
	$b = rand($b-20, $b+20);
	$base_color = imagecolorallocate($image, $r, $g, $b);
	$offset = rand(1,13);
	for($i=0; $i < 150; $i++)
	{
		$x = $j*28+10*sin($i/$period)+$offset;
		imagesetpixel($image, $x+($i/10), $i, $base_color);
		imagesetpixel($image, $x+($i/10)+1, $i, $base_color);
	}
}



header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
unset($passphrase);
?>