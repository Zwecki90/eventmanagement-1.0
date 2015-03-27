<?php
	session_start();

	$config = parse_ini_file('../config.ini', true);
	require_once './includes/db-connect.php';
	require_once './includes/user.php';
	
	$email = $_POST['email'];
	$password = md5($_POST['password']);
	
	$stmt = $db->prepare('SELECT id, email, access, active FROM access WHERE email = :email AND password = :password;');
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->bindParam(':password', $password, PDO::PARAM_STR);
	$stmt->execute();
	
	if ($stmt->rowCount() < 1)
	{
		return printf('Dieser Benutzer existiert nicht. Bitte überprüfen Sie Ihre Eingaben!');
	}
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if ($row['active'] < 1)
	{
		return printf('Bitte aktivieren Sie zunächst Ihren Account!');
	}
	
	try
	{
		$user = new User($row['id'], $row['email'], $row['access'], $row['active']);
		$_SESSION['user'] = serialize($user);
		return printf('success');
	}
	catch(Exception $e)
	{
		return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
	}