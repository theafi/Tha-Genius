<?php
    session_start();
    

	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header("Location: ../login.php");
	}
    require '../funcion.php';
    $consulta = new Consultas;
    if(isset($_GET['dominio'])) {
        if ($_GET['dominio'] === $_SESSION['dominio']) {
            $consulta->cerrar();
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>No puedes borrarte a ti mismo</div>";        
        } else {
            $dominio = $consulta->escapar($_GET['dominio']);
            $consulta->preparar('DELETE FROM domains WHERE domain = ?', $dominio, 's');
        }

    } elseif (isset($_POST['checkbox'])) {
        if (empty($_POST['token'])) {
            header("Location: ../");
        }
        elseif (!hash_equals($_SESSION['token'], $_POST['token'])) {
                header("Location: ../");
            } 
    } else {
         foreach ($_POST['checkbox'] as $dominio) {
            if ($dominio === $_SESSION['dominio']) {
                $consulta->cerrar();
                $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>No puedes borrarte a ti mismo</div>";
            } else {
                $consulta->preparar('DELETE FROM domains WHERE domain = ?', $dominio, 's');  
            }
        }
    }
    $consulta->cerrar();
    header('Location: ../dominios.php');


?>