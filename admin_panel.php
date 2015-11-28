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
		// 128s bit (2^7) set: can select team leaders and assign teams
		// 256s bit (2^7) set: can scramble/reset a user's password
		switch(@$_GET['func'])
		{
			case "teamLeaders":
				if(($_SESSION['permissions'] & 8) == true)
				{
					echo '	<head>
		<meta charset="urf-8">
		<link rel="stylesheet" type="text/css" href="custom.css.php">
		<title>Admin Controls - Create Teams</title>
	</head>
	<body>
		<div id="page_content">
			<div id="banner">
				<h1>Ivy-League</h1>
				<h3>Scholarship Tracking System</h3>
			</div>
			<div id="navbar">'."\n";
				print_navbar_items();
					echo "\n".'			</div>
			<div id="container">
				<form method="POST" action="" style="margin: 0 auto; width: 60%">
					<h3>Select members of you team to be team leaders.</h3>
					<p>Teams will be created with the name of the team leader. Team Leaders will be given a single opportunity to rename their team.</p>
					<select size="20" multiple style="display: inline-block;">'."\n";
					$teamInfo = getTeam($_SESSION['uid']);
					$link = connect_db_read();
					if($stmt = mysqli_prepare($link, "SELECT id, first_name, last_name FROM users WHERE org = ? AND permissions = 0"))
					{
						mysqli_stmt_bind_param($stmt, "i", $teamInfo['teamOrg']);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_bind_result($stmt, $uid, $uFirstName, $uLastName);
						while(mysqli_stmt_fetch($stmt))
						{
							echo '						<option value="'.$uid.'">'.$uLastName.', '.$uFirstName.'</option>'."\n";
						}
						mysqli_stmt_close($stmt);
					}
					echo '					</select>
					<table border="0" style="display: inline-block; vertical-align: 125px;">
						<tr><td><button type="button" style="padding: 15px;">&gt;</button></td></tr>
						<tr><td><button type="button" style="padding: 15px;">&lt;</button></td></tr>
					</table>
					<select size="20" multiple style="display: inline-block;">

					</select>
				</form>
			</div>
		</div>'."\n";
				}else
					echo '<h2>Permission Error: You do not have permission to view this page.</h2>';
			break;
			
			case "report":
				echo '	<head>
		<meta charset="urf-8">'."\n";
				switch($_GET['type'])
				{
					case "ind":
						// determine if the user has team or global privs
						// if they have global priveledges the team doesn't matter...
						// but still make sure the user is part of the org
						$link = connect_db_read();
						if(($_SESSION['permissions'] & 32) == true || ($_SESSION['permissions'] & 64) == true)
						{
							// User has global viewing privs for the org
							if($stmt = mysqli_prepare($link, "SELECT (u1.org = u2.org) AS equals FROM `users` AS u1 INNER JOIN `users` AS u2 ON u1.org = u2.org AND u1.id = ? AND u2.id = ?"))
							{
								// if the stmt returns a 1 (true) it means that user 1 and user 2 are in the same org
								mysqli_stmt_bind_param($stmt, "ii", $_SESSION['uid'], $_GET['member']);
								mysqli_stmt_execute($stmt);
								mysqli_stmt_bind_result($stmt, $sameOrg);
								mysqli_stmt_fetch($stmt);
								mysqli_stmt_close($stmt);
								if($sameOrg)
								{
									$member = new user($_GET['member']); // a new user object is created and populated with courses, categories and assignments
									$teamInfo = getTeam($_GET['member']);
									$team = array();
									/* echo '<pre>';
									print_r($member); */
									
									// Display the information gathered from the database in a nicely formatted table.
									echo '		<title>Ivy League: Member Report</title>
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
				<td colspan="3">Organization Name Goes Here</td>
				<td colspan="2">&nbsp;</td>
				<td>Time: </td>
				<td>'.date('g:i:s A').'</td>
			</tr>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>';
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
								}else
									echo '<h2>Permission Error: You do not have permission to view this member because they are not in your league.</h2>';
							}
						// if they have team priveledges make sure the requested UID is on their team
						}elseif(($_SESSION['permissions'] & 2) == true || ($_SESSION['permissions'] & 4) == true)
						{
							$teamInfo = getTeam($_SESSION['uid']);
							$team = array();
							$teamMembers = getTeamMembers($teamInfo['teamID']); // gets UID, first name, and last name of each member of the team
							if(in_array_r($_GET['member'], $teamMembers)) // they are members of the same team
							{
								$member = new user($_GET['member']); // a new user object is created and populated with courses, categories and assignments
								
								echo '		<title>Ivy League: Member Report</title>
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
				<td colspan="3">Organization Name Goes Here</td>
				<td colspan="2">&nbsp;</td>
				<td>Time: </td>
				<td>'.date('g:i:s A').'</td>
			</tr>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>';
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
							}else{ // they are not members of the same team and an error should be displayed
								echo '<h2>Permission Error: You do not have permission to view this member because they are not on your team.</h2>';
							}
						}

					break; // end ind report case
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
<?php
				if(($_SESSION['permissions'] & 2) == true || ($_SESSION['permissions'] & 4) == true || ($_SESSION['permissions'] & 32) == true || ($_SESSION['permissions'] & 64) == true)
				{
					if(($_SESSION['permissions'] & 2) == true || ($_SESSION['permissions'] & 4) == true)
						echo '					<li><a href="admin_panel.php?func=report&type=team" target="_blank">View/Print Team Report</a></li>'."\n";
						echo '					<li>View/Print Individual Report: <form style="display:inline-block;" method="GET" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'"" id="memberReportSelect">
							<input type="hidden" name="func" value="report" />
							<input type="hidden" name="type" value="ind" />
							<select onchange="this.form.submit()" name="member">
							<option value="" selected></option>'."\n";
						
						if(($_SESSION['permissions'] & 32) == true || ($_SESSION['permissions'] & 64) == true)
						{
							if($link = connect_db_read())
								if($result = mysqli_query($link, "SELECT id as uid, first_name, last_name FROM users WHERE org = (SELECT org FROM users WHERE id = ".$_SESSION['uid'].") ORDER BY last_name ASC"))
								{
									while($row = mysqli_fetch_assoc($result))
									{
										$members[] = array('uid'=>$row['uid'], 'first_name'=>$row['first_name'], 'last_name'=>$row['last_name']);
									}
								}else
									echo 'Result failed';
						}elseif(($_SESSION['permissions'] & 2) == true || ($_SESSION['permissions'] & 4) == true)
							$members = getTeamMembers(getTeam($_SESSION['uid']));
						foreach($members as $memb)
						{
							echo '								<option value="'.$memb['uid'].'">'.$memb['last_name'].', '.$memb['first_name'].'</option>'."\n";
						}
							echo '							</select></form></li>'."\n";
				}
?>
				
						
				</ul>
					<h3><u>Team Options</u></h3>
<?PHP				if(($_SESSION['permissions'] & 128) == true)
					{
						echo '				<ol>
					<li><a href="admin_panel.php?func=teamLeaders">Select Team Leaders</a></li>
					<li><a href="admin_panel.php?func=teams">Assign Teams</a></li>
				</ol>';
					}
					if(($_SESSION['permissions'] & 1) == true)
					{
						echo '				<ul>
					<li>Edit Team Name (One Time Only)</li>
				</ul>';
					}
?>
					
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