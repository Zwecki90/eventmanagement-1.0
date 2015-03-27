﻿<?php
	$config = parse_ini_file('../config.ini', true);
	require_once './includes/db-connect.php';
	require_once './includes/functions.php';
	
	$email = $_POST['email'];
	$access = $_POST['access'];
	$password = md5($_POST['password']);
	$password2 = md5($_POST['password2']);
	
	if ($password !== $password2)
		return printf('error_no_match');
		
	$stmt = $db->prepare('SELECT DISTINCT id FROM access WHERE email = :email');
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->execute();
	
	if ($stmt->rowCount() > 0)
	{
		return printf('error_email_used');
	}
		
	$code = rand(1, 99999999);
	
	$stmt = $db->prepare('INSERT INTO access (email, password, access, code, active) VALUES (:email, :password, :access, :code, false);');
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->bindParam(':password', $password, PDO::PARAM_STR);
	$stmt->bindParam(':access', $access, PDO::PARAM_STR);
	$stmt->bindParam(':code', $code, PDO::PARAM_STR);
	$stmt->execute();
	
	$id = $db->lastInsertId();
	
	mail($email, 'Registrierung abschließen', 'Hallo,\n\num die Registrierung abzuschließen, klicken Sie bitte auf den folgenden Link:n\n'.url_origin($_SERVER).'/eventmanagement/index.php?id='.$id.'&code='.$code, 'FROM:no-replay@'.get_site_host($_SERVER));
    return printf("<div class=\"event-management-body-content-text-container\">Um die Registrierung abzuschließen, rufen Sie Ihr E-Mail-Postfach ab und klicken Sie auf den Aktivierungslink in der soeben an Sie versandten E-Mail.</div>");