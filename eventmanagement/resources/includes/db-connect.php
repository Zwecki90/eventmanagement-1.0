<?php
	$dbconfig = $config['Database'];

	$db = new PDO('mysql:host='.$dbconfig['host'].';port='.$dbconfig['port'].';dbname='.$dbconfig['database'].';charset=utf8', $dbconfig['user'], $dbconfig['password']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
?>