<?php
    session_start();
    require '../funcion.php';
    $consulta = new Consultas;
    if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		$consulta->cerrar();
        header('Location: login.php');
	 }
    if (!isset($_POST['token'])){
        $consulta->cerrar();
        header("Location: ../index.php");
    }
    if($_POST['token'] !== $_SESSION['token']) {
        $consulta->cerrar();
        header("Location: ../index.php");
    }
    $dominio = $consulta->escapar($_POST['dominio']);
    $consulta->preparar("INSERT INTO `domains` (`domain`) VALUES (?)", $dominio, 's');
    $consulta->cerrar();
    header("Location: ../dominios.php");
    ?>