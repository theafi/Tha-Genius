<?php
	session_start();
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) { 
		header('Location: login.php');
	} else{
		require 'funcion.php';
		$fecha = date('Y-m-d H:i:s');
		$email = $_SESSION['email'];
		$consulta = new Consultas;
		$email = $consulta -> escapar($email);
		$consulta->preparar("UPDATE sessions SET last_logout = '$fecha' WHERE email = ?", $email, 's');
		$consulta->preparar("UPDATE sessions SET last_login = current_login WHERE email = ?", $email, 's');
		$consulta->preparar("UPDATE sessions SET current_login = NULL WHERE email = ?", $email, 's');
		$consulta->cerrar();
		$_SESSION[] = array();
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
		session_regenerate_id(true);
		header('Location: login.php');
	}
	
	
?>