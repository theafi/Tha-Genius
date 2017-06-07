<?php
    session_start();

	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header("Location: ../login.php");
	}
    require '../funcion.php';
    $consulta = new Consultas;
    if(isset($_GET['email'])) {
        if ($_GET['email'] === $_SESSION['email']) {
            $consulta->cerrar();
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>No puedes borrarte a ti mismo</div>";        
        } else {
            $email = $consulta->escapar($_GET['email']);
            $consulta->preparar('DELETE FROM users WHERE email = ?', $email, 's');
        }

    } elseif (isset($_POST['checkbox'])) {
        if (empty($_POST['token'])) {
            header("Location: ../");
        }elseif (!hash_equals($_SESSION['token'], $_POST['token'])) {
                header("Location: ../");
        } else { 
        
         foreach ($_POST['checkbox'] as $email) {
            if ($email === $_SESSION['email']) {
                $consulta->cerrar();
                $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>No puedes borrarte a ti mismo</div>";
            } else {
                $consulta->preparar('DELETE FROM users WHERE email = ?', $email, 's');  
            }
        }
    }
    $consulta->cerrar();
    header('Location: ../usuarios.php');


?>