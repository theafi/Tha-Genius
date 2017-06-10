<?php
	session_start();
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) { 
		header('Location: login.php');
	} else{
		require 'funcion.php';
		$fecha = date('Y-m-d H:i:s');
		$sessionid = $_SESSION['sessionid'];
		$consulta = new Consultas;
		$email = $consulta -> escapar($email);
		$consulta->preparar("UPDATE sessions SET last_logout = '$fecha' WHERE sessionid = ?", $sessionid, 's');
		$consulta->preparar("UPDATE sessions SET last_login = current_login WHERE sessionid = ?", $sessionid, 's');
		$consulta->preparar("UPDATE sessions SET current_login = NULL WHERE sessionid = ?", $sessionid, 's');
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