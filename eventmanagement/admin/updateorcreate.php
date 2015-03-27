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
	
	// Titel erfassen
	if (!isset($_POST['title']) || empty($_POST['title']))
	{
		return printf('Es ist ein Fehler aufgetreten: Es wurden nicht alle benötigten Parameter übergeben!');
	}
	$title = $_POST['title'];
	
	// Beschreibung erfassen
	if (!isset($_POST['description']) || empty($_POST['description']))
	{
		return printf('Es ist ein Fehler aufgetreten: Es wurden nicht alle benötigten Parameter übergeben!');
	}
	$desc = $_POST['description'];
	
	// Datum und Uhrzeit erfassen
	if (!isset($_POST['datetime']) || empty($_POST['datetime']))
	{
		return printf('Es ist ein Fehler aufgetreten: Es wurden nicht alle benötigten Parameter übergeben!');
	}
	$datetime = $_POST['datetime'];
	
	// max. Teilnehmerzahl erfassen
	if (!isset($_POST['maxcount']) || empty($_POST['maxcount']))
	{
		return printf('Es ist ein Fehler aufgetreten: Es wurden nicht alle benötigten Parameter übergeben!');
	}
	$maxcount = $_POST['maxcount'];
	
	require_once '../resources/includes/functions.php';
	
	// Datentypen und Werte überprüfen
	if (!is_numeric($maxcount))
	{
		return printf('Es ist ein Fehler aufgetreten: Die maximale Teilnehmeranzahl muss eine Zahl sein!');
	}
	elseif ($maxcount < 1)
	{
		return printf('Es ist ein Fehler aufgetreten: Die maximale Teilnehmeranzahl muss mindestens 1 sein!');
	}
	
	$datetocheck = strtotime($datetime);
	if (!$datetocheck || !checkdate(date('m', $datetocheck), date('d', $datetocheck), date('Y', $datetocheck	)) || $datetocheck - mktime() < 0)
	{
		return printf('Es ist ein Fehler aufgetreten: Das angegebene Datum ist ungültig! Bitte beachten Sie, dass Datum und Uhrzeit nicht in der Vergangenheit liegen dürfen!');
	}
	
	if (!checktime(date('H', $datetocheck), date('m', $datetocheck)))
	{
		return printf('Es ist ein Fehler aufgetreten: Die angegebene Uhrzeit ist ungültig!');
	}
	
	$config = parse_ini_file('../config.ini', true);
	require_once '../resources/includes/db-connect.php';
	
	if (!isset($_POST['id']) || empty($_POST['id']))
	{
		try
		{
			$stmt = $db->prepare('INSERT INTO event (title, description, date, max_count, ptr2owner) VALUES (:title, :desc, :datetime, :maxcount, :ownerid);');
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
			$stmt->bindParam(':datetime', date('Y-m-d H:i:s', $datetocheck), PDO::PARAM_STR);
			$stmt->bindParam(':maxcount', $maxcount, PDO::PARAM_INT);
			$stmt->bindParam(':ownerid', $user->id, PDO::PARAM_INT);
			$stmt->execute();
			
			return printf("success");
		}
		catch(Exception $e)
		{
			return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
		}
	}
	else
	{
		try
		{
			$id = $_POST['id'];
			$stmt = $db->prepare('SELECT id FROM participation WHERE ptr2eventid = :id');
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() > $maxcount)
			{
				throw new Exception('Die Zahl der aktuellen Teilnehmer ist höher als die neue maximale Teilnehmerzahl!');
			}
			
			$stmt = $db->prepare('UPDATE event SET title = :title, description = :desc, date = :datetime, max_count = :maxcount, ptr2owner = :ownerid WHERE id = :id;');
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
			$stmt->bindParam(':datetime', date('Y-m-d H:i:s', $datetocheck), PDO::PARAM_STR);
			$stmt->bindParam(':maxcount', $maxcount, PDO::PARAM_INT);
			$stmt->bindParam(':ownerid', $user->id, PDO::PARAM_INT);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			
			return printf("success");
		}
		catch(Exception $e)
		{
			return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
		}
	}