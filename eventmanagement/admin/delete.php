<?php
	session_start();
	require_once '../resources/includes/user.php';
	
	if (!isset($_SESSION['user']) || empty($_SESSION['user']))
	{
		return printf('error_no_login');
	}
	$user = unserialize($_SESSION['user']);
	
	if ($user->access < 2)
	{
		return printf('error_no_access');
	}
	
	if (!isset($_POST['eventid']) || empty($_POST['eventid']))
	{
		return printf('Es ist ein Fehler aufgetreten: Die Veranstaltung konnte nicht gelöscht werden!');
	}
	$id = $_POST['eventid'];
	
	try
	{
		$config = parse_ini_file('../config.ini', true);
		require_once '../resources/includes/db-connect.php';
		
		$stmt = $db->prepare('DELETE FROM participation WHERE ptr2eventid = :id;');
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt = $db->prepare('DELETE FROM event WHERE id = :id;');
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		return printf("success");
	}
	catch(Exception $e)
	{
		return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
	}