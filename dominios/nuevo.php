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
    $patron = "/[^.]+\.[^.]+$/";
    $dominio = $consulta->escapar($_POST['dominio']);
    if (preg_match($patron, $dominio)) { // Compara el patrón con el valor a insertar y comprueba que coinciden. Hago esto porque la comprobación por parte del cliente me da muchos problemas y no funciona al 100%
        $consulta->preparar("INSERT INTO `domains` (`domain`) VALUES (?)", $dominio, 's');
    } else {
        $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>". $dominio. " no es un dominio válido.</div>"; 

    }
    
    $consulta->cerrar();
    header("Location: ../dominios.php");
    ?>