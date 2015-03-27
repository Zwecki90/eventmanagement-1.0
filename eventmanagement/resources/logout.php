<?php
	session_start();

	try
	{
		$_SESSION['user'] = '';
		unset($_SESSION['user']);
	
		return printf('success');
	}
	catch(Exception $e)
	{
		return printf('Es ist ein Fehler aufgetreten: %s', $e->getMessage());
	}