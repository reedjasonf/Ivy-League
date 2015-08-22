<?php
include_once('common_functions.php');
include_once('grade_functions.php');
include_once('objects/class_def.php');
sec_session_start();
if(COMPRESSION == TRUE){
	ob_start("ob_gzhandler");
}
?>
<!DOCTYPE html>
<html lang='en' dir='ltr'>
<?php
if(login_check())
{
	// logged in correctly	
	// display the admin page
	// first check if the user should be looking at the page
	// (ie do they have any special permissions?)
	if($_SESSION['permissions'] == 0 || !isset($_SESSION['permissions']))
	{
		// they don't have special permissions and should not be on this page
?>
	<head>
		<meta charset="urf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Error: Permission Denied</title>
	</head>
	<body id="loginError">
		<div id="page_content">
			<div id="banner">
				<h1>Ivy-League</h1>
				<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container">
				<h2>You do not have the permissions to be here.</h2>
				<h3>Security Error: You are trying to access a page you do not have priveledges to view.</h3>
<?php
	$entry = "Invalid permissions for ".$_SERVER['PHP_SELF']." from IP ".$_SERVER['REMOTE_ADDR'].". Logged in with UID:".$_SESSION['uid'].". [".date_with_micro('Y-m-d H:i:s:u')."]\n";
	file_put_contents("logs/security.txt", $entry, FILE_APPEND | LOCK_EX);
?>
			</div>
		</div>
<?php

	}else{
		// they have SOME level of special permissions. Based on the permission integer show them what they can do.
		// 1s bit (2^0) set: can edit team name
		// 2s bit (2^1) set: see team member points
		// 4s bit (2^2) set: see team member grades
		// 8s bit (2^3) set: can assign team leaders
		// 16s bit (2^4) set: can assign team members
		// 32s bit (2^5) set: can see any member of the org/league's points
		// 64s bit (2^6) set: can see any member of the org/league's grades
		// 128s bit (2^7) set: can scramble/reset a user's password
		switch(@$_GET['func'])
		{
			case "report":
				echo '	<head>
		<meta charset="urf-8">'."\n";
				switch($_GET['type'])
				{
					case "team":
						if(($_SESSION['permissions'] & 2) == true || ($_SESSION['permissions'] & 4) == true)
						{
							$teamInfo = getTeam($_SESSION['uid']);
							$team = array();
							// query for all members of the team (current user's team) and display each
							$teamMembers = getTeamMembers($teamInfo['teamID']); // gets UID, first name, and last name of each member of the team
							foreach($teamMembers as $person)
							{
								// put users into the team array using the objects
								$team[] = new user($person['uid']);
								// each member of the team will already have their courses pulled at this point
								// all course information is pulled from db... output table
							}
							
							echo '		<title>Ivy League: Team Report</title>
		<style>
			.transparent40 {
				opacity: 0.4;
				filter: Alpha(opacity=40); /* IE8 and earlier */
			}
			
			table, td, th {
				border: 1px solid black;
				margin-left: auto;
				margin-right: auto;
				border-collapse: collapse;
			}
		</style>
	</head>
	<body>
		<table>
			<tr>
				<td colspan="2">Team Name:</td>
				<td colspan="3">'.$teamInfo['teamName'].'</td>
				<td colspan="2">&nbsp;</td>
				<td>Date: </td>
				<td>'.date('n/j/Y').'</td>
			</tr>
			<tr>
				<td colspan="2">League for:</td>
				<td colspan="3">Sigma Nu, Gamma Xi</td>
				<td colspan="2">&nbsp;</td>
				<td>Time: </td>
				<td>'.date('g:i:s A').'</td>
			</tr>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="9"><h3 style="line-height:1.5;margin:0;padding:0;">Grades</h3></td>
			</tr>'."\n";
							foreach($team as $member)
							{
								echo '			<tr>
				<td colspan="3">'.$member->fname.' '.$member->lname.'</td>
				<td colspan="6">'.count($member->courses).' Course'; echo count($member->courses) != 1 ? 's' : ''; echo '</td>
			</tr>'."\n";
								foreach($member->courses as $course)
								{
									echo '			<tr>
				<td style="border-bottom:0;">&nbsp;</td>
				<td colspan="4">'.$course->title.' - '.$course->cn.' ('.$course->credits.' credits)</td>
				<td>'.number_format($course->earnedPoints, 1).'</td>
				<td style="padding: 0 4px;"> / </td>
				<td>'.number_format($course->totalPoints-$course->inactivePoints).'</td>
				<td>'.number_format($course->currentGrade*100, 2).'%</td>
			</tr>'."\n";
									foreach($course->categories as $category)
									{
										echo '			<tr>
				<td style="border-top:0;border-right:0;border-bottom:0;">&nbsp;</td>
				<td style="border-left:0;border-bottom:0;border-top:0;">&nbsp;</td>
				<td>'.$category->title.'</td>
				<td colspan="2">'.count($category->assignments).' assignment'; echo count($category->assignments) != 1? 's' : ''; echo '</td>
				<td>'; echo $category->totalOffered == 0 ? '&nbsp;' : number_format($category->catPoints(), 1); echo '</td>
				<td>/</td>
				<td>'.$category->maxPoints.'</td>
				<td>'; echo $category->catPoints() > 0 ? number_format($category->catPoints()/$category->maxPoints*100, 1):0; echo'%</td>
			</tr>'."\n";
										foreach($category->assignments as $index=>$assignment)
										{
											
											echo '			<tr'; echo @(($assignment->earned/$assignment->denom) <=0.7 && $assignment->denom > 0) ? ' style="border-color:black;color:red;font-weight:bold;"': ''; echo '>';
											echo "\n".'				<td colspan="3" style="border-top:0;border-bottom:0;">&nbsp;</td>
				<td';
											if($assignment->dropped)
												echo $assignment->dropped ? ' class="transparent40"><s>':'>';
											else echo '>';
											echo ($index++);
											if($assignment->dropped)
												echo '</s>';
											echo '. </td>
				<td';
											if($assignment->dropped)
												echo $assignment->dropped ? ' class="transparent40"><s>':'>';
											else echo '>';
											echo $assignment->description;
											if($assignment->dropped)
												echo '</s>';
											echo '</td>
				<td';
											if($assignment->dropped)
												echo $assignment->dropped ? ' class="transparent40"><s>':'>';
											else echo '>';
											echo $assignment->earned;
											if($assignment->dropped)
												echo '</s>';
											echo '</td>
				<td>/</td>
				<td';
											if($assignment->dropped)
												echo $assignment->dropped ? ' class="transparent40"><s>':'>';
											else echo '>';
											echo $assignment->denom;
											if($assignment->dropped)
												echo '</s>';
											echo '</td>
				<td>&nbsp;</td>
			</tr>'."\n";
										}
									}
								}
							}
						}else
							echo 'You don\'t have permissions to view reports '.$_SESSION['permissions'];
						// get the team id for the logged in user
					break; // end team report case
					
					case "league":
						echo '		<title>Ivy League: League Report</title>
	</head>
	<body>'."\n";
					break; // end league report case
				}
			break; // end report case
			default:
?>
	<head>
		<meta charset="urf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Ivy League: Admin Controls</title>
	</head>
	<body>
		<div id="page_content">
			<div id="banner">
				<h1>Ivy-League</h1>
				<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>

			</div>
			<div id="container">
				<h2>Admin Control Panel</h2>
				<h3>Available Commands:</h3>
				<br>
				<h3><u>Tools:</u></h3>
					<ul>
						<li><a href="admin_panel.php?func=report&type=team" target="_blank">View/Print Team Report</a></li>
			</div>
		</div>
<?php
			break; // end default func case
		}
	}
}else{
	// not logged in.
?>
	<head>
		<meta charset="urf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Error: Not Logged In</title>
	</head>
	<body id="loginError">
		<div id="page_content">
			<div id="banner">
			<h1>Ivy-League</h1>
			<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">
				<?php print_navbar_items(); ?>
			</div>
			<div id="container">
				<h2>You're not even logged in.</h2>
				<h3>Security Error: You are trying to access a secure page while not logged in.</h3>
			</div>
		</div>
<?php
	//echo $_SESSION['uid'].' '.$_SESSION['login_string'];
	$entry = "Direct visit to ".$_SERVER['PHP_SELF']." from IP ".$_SERVER['REMOTE_ADDR'].". [".date_with_micro('Y-m-d H:i:s:u')."]\n";
	file_put_contents("logs/security.txt", $entry, FILE_APPEND | LOCK_EX);
}
?>
	</body>
</html>