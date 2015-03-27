<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	
	$config = parse_ini_file('./config.ini', true);
	require_once './resources/includes/db-connect.php';
	require_once './resources/includes/user.php';
	
	$logged_in = false;
	if (isset($_SESSION['user']) && unserialize($_SESSION['user'])->access > 0)
	{
		$user = unserialize($_SESSION['user']);
		$logged_in = true;
	}
	
	$activation_pending = false;
	if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['code']) && !empty($_GET['code']))
		$activation_pending = true;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Veranstaltungs-Verwaltung</title>
		
		<link rel="stylesheet" type="text/css" href="./resources/format.css" />
		<link rel="stylesheet" type="text/css" href="./resources/jquery.datetimepicker.css" />
		<script type="text/javascript" language="JavaScript" src="./resources/jquery.min.js"></script>
		<script type="text/javascript" language="JavaScript" src="./resources/jquery.datetimepicker.js"></script>
		<script type="text/javascript" language="JavaScript" src="./resources/script.js"></script>
	</head>
	<body>
		<noscript>Diese Seite benötigt Javascript. Bitte aktivieren Sie Javascript in Ihrem Browser</noscript>
		<div class="event-management-body">
			<div class="event-management-body-head">
				<div class="event-management-body-head-nav">
					Navigation: <span class="event-management-body-head-nav-links"><a href="./index.php">&rArr; Liste</a> <?=($logged_in && $user->access > 1) ? ' | <a href="./index.php?admin=1">&rArr; Verwaltung</a>' : ''?></span>
				</div>
				<div class="event-management-body-head-login">
					<?php
						if ($logged_in)
						{
							printf('Eingeloggt als <span class="bold">%s</span> | <button type="button" class="event-management-body-head-login-logout">ausloggen</button>', $user->email);
						}
						elseif (!$activation_pending)
						{
							echo '
							<button type="button" class="event-management-body-head-login-register">Registrieren</button> |
							<form action="#" method="post" class="event-management-body-head-login-form"> 
								<label for="eventManagementBodyHeadLoginFormEmail">Email:</label>
								<input type="email" name="email" id="eventManagementBodyHeadLoginFormEmail">
								<label for="eventManagementBodyHeadLoginFormPassword">Password:</label>
								<input type="password" name="password" id="eventManagementBodyHeadLoginFormPassword">
								<input type="submit" name="login" id="eventManagementBodyHeadLoginFormSubmit" value="Login">
							</form>';
						}
					?>
					
				</div>
			</div>
			<div class="event-management-body-content">
				<?php
					if ($activation_pending)
					{
						include './resources/includes/activate-acc.php';
					}
					elseif(isset($_GET['admin']) && intval($_GET['admin']) === 1)
					{
						include './admin/admin.php';
					}
					else
					{
						include './resources/includes/event-list.php';
					}
				?>
			</div>
		</div>
	</body>
</html>