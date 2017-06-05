<?php
	session_start();
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) { 
		header('Location: login.php');
	} else{
		require 'funcion.php';
		$fecha = date('Y-m-d H:i:s');
		$consulta = new Consultas;
		$consulta->consulta("UPDATE sessions SET last_logout = '{$fecha}' WHERE session_id = {$_SESSION['sessionid']}");
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