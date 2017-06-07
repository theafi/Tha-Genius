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
    if(!isset($_POST['origen']) || !isset($_POST['destino'])) {
        echo "error";
    } elseif($_POST['origen'] === $_POST['destino']) {
        echo "error";
    } else {
        $origen = $consulta->escapar($_POST['origen']);
        $destino = $consulta->escapar($_POST['destino']);
        $consulta->consulta("INSERT INTO forwardings(source, destination) VALUES ('$origen', '$destino')");
    }
    
    $consulta->cerrar();
    header("Location: ../redirecciones.php");
    ?>