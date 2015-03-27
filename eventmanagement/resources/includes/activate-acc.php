<?php
	$id = $_GET['id'];
	$code = $_GET['code'];
	
	if (empty($id))
		return printf("<div class=\"event-management-body-content-text-container\">Es ist ein Fehler aufgetreten: Es wurde keine ID übermittelt. Bitte überprüfen Sie, ob der Link in der Adresszeile mit dem aus Ihrer Email übereinstimmt!</div>");
	
	if (empty($code))
		return printf("<div class=\"event-management-body-content-text-container\">Es ist ein Fehler aufgetreten: Es wurde kein Aktivierungscode übermittelt. Bitte überprüfen Sie, ob der Link in der Adresszeile mit dem aus Ihrer Email übereinstimmt!</div>");
		
	$stmt = $db->prepare('UPDATE access SET active = TRUE WHERE id = :id AND code = :code');
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->bindParam(':code', $code, PDO::PARAM_STR);
	$stmt->execute();
	
	printf('<div class="event-management-body-content-text-container">');
	if ($stmt->rowCount() < 1)
	{
		printf("Es ist ein Fehler aufgetreten: Der Datensatz konnte nicht aktualisiert werden!");
	}
	else
	{
		$stmt = $db->prepare('SELECT id, email, access, active FROM access WHERE id = :id');
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() < 1)
		{
			printf('Login fehlgeschlagen! Klicken Sie bitte <a href="./index.php">hier</a> und loggen sich manuell ein.');
		}
		else
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$_SESSION['user'] = serialize(new User($row['id'],$row['email'], $row['access'], $row['active']));
			printf('Die Aktivierung war erfolgreich. Klicken Sie bitte <a href="./index.php">hier</a>, um zur Startseite zurückzukehren.');
		}
	}
	printf('</div>');