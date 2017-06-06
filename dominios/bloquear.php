<?php
    session_start();
    require '../funcion.php';
    $consulta = new Consultas;
    if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		$consulta->cerrar();
        header('Location: ../login.php');
	 }
	if (empty($_SESSION['token'])) { // Para evitar ataques CSRF
		header('Location: ../index.php');
	}
    	if (empty($_GET['dominio'])) { 
        $consulta->cerrar();
		header('Location: ../dominios.php');
	}
    
    $dominio = $consulta->escapar($_GET['dominio']);
    $consultaBloqueado = $consulta->preparar("SELECT block FROM domains WHERE domain = ?", $dominio, 's');
    $bloqueado = $consultaBloqueado->fetch_array(MYSQLI_NUM);
    if ($bloqueado[0] === 1 ) {
        $consulta->preparar("UPDATE domains SET block = '0' WHERE domain = ?", $dominio, 's');
    } else {
        $consulta->preparar("UPDATE domains SET block = '1' WHERE domain = ?", $dominio, 's');
    }
    
    $consulta->cerrar();
    header("Location: ../dominios.php");
    ?>