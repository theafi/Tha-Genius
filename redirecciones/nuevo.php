<?php
    session_start();
    require '../funcion.php';
    $consulta = new Consultas;
    if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		$consulta->cerrar();
        header('Location: login.php');
	} elseif (!isset($_POST['token'])){
        $consulta->cerrar();
        header("Location: ../index.php");
    }elseif($_POST['token'] !== $_SESSION['token']) {
        $consulta->cerrar();
        header("Location: ../index.php");
    } else {
    if(!isset($_POST['origen']) || !isset($_POST['destino'])) {
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>Falta uno de los parámetros.</div>"; 
        } elseif($_POST['origen'] === $_POST['destino']) {
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>No puedes anadir el mismo origen y destino.</div>"; 
        } elseif (!filter_var($_POST['destino'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>{$_POST['destino']} no es un correo electrónico.</div>";
        } else {
            $origen = $consulta->escapar($_POST['origen']);
            $destino = $consulta->escapar($_POST['destino']);
            $consultaOrigen = $consulta->preparar("SELECT source FROM forwardings WHERE source = ?", $origen, 's');
            $row = $consultaOrigen->fetch_array(MYSQLI_NUM);
            if ($row[0] !== $origen) {
                $consulta->consulta("INSERT INTO forwardings(source, destination) VALUES ('$origen', '$destino')");
            } else {
                $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>{$origen} ya está registrado. Borra la redirección previa si quieres designar una nueva.</div>";
            }
            
        }
        
    $consulta->cerrar();
    header("Location: ../redirecciones.php");
    }
    
    ?>