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
		
		$stmt = $db->prepare('SELECT COUNT(p.id) AS p_count, e.max_count FROM event e LEFT JOIN participation p ON p.ptr2eventid = e.id WHERE e.id = :eventid GROUP BY e.max_count;');
		$stmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
		$stmt->execute();
		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['p_count'] >= $row['max_count'])
		{
			return printf('limit_reached_error');
		}
		
		$stmt = $db->prepare('SELECT id FROM participation WHERE ptr2eventid = :eventid AND ptr2accessid = :accessid');
		$stmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
		$stmt->bindParam(':accessid', $user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount())
		{
			return printf('already_part_error');
		}
		
		$stmt = $db->prepare('INSERT INTO participation (ptr2eventid, ptr2accessid) VALUES (:eventid, :accessid);');
		$stmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
		$stmt->bindParam(':accessid', $user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		return printf('﻿success');
	}
	catch(Exception $e)
	{
		return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
	}