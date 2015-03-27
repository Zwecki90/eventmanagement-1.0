<?php
	session_start();

	$config = parse_ini_file('../config.ini', true);
	require_once './includes/db-connect.php';
	require_once './includes/user.php';
	
	if (!isset($_SESSION['user']) || empty($_SESSION['user']))
	{
		return printf('Es ist ein Fehler aufgetreten: Sie sind nicht eingeloggt!');
	}
	
	try
	{
		$user = unserialize($_SESSION['user']);
		$eventid = $_POST['eventid'];
		
		$stmt = $db->prepare('DELETE FROM participation WHERE ptr2eventid = :eventid AND ptr2accessid = :accessid;');
		$stmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
		$stmt->bindParam(':accessid', $user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		return printf('﻿success');
	}
	catch(Exception $e)
	{
		return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
	}