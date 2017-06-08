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
    } elseif(!isset($_POST['dominio']) || !isset($_POST['transporte'])) {
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>Falta uno de los parámetros.</div>"; 
    } else {
            $dominio = $consulta->escapar($_POST['dominio']);
            $transporte = $consulta->escapar($_POST['transporte']);
            $consultaDominio = $consulta->preparar("SELECT domain FROM transport WHERE domain = ?", $dominio, 's');
            $row = $consultaDominio->fetch_array(MYSQLI_NUM);
            if ($row[0] !== $dominio) {
                if (filter_var($_POST['transporte'], FILTER_VALIDATE_IP)) {
                    $consulta->consulta("INSERT INTO transport(domain, transport) VALUES ('$dominio', 'smtp:[$transporte]')"); // Los corchetes son para que postfix no busque el registro MX en las DNS de la dirección
                    } 
                elseif (preg_match("/[^.]+\.[^.]+$/", $_POST['transporte'])) {
                    $consulta->consulta("INSERT INTO transport(domain, transport) VALUES ('$dominio', 'smtp:$transporte')");
                } else {
                    $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>{$transporte} no es una IP o FQDN válida.</div>";

                }
            } else {
                $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>{$dominio} ya está registrado.</div>";
            }
            $consulta->cerrar();
            header("Location: ../transporte.php");
        }
        
    
    
    ?>