<?php
    session_start();
    
    if (empty($_POST['token'])) {
        header("Location: ../");
        }
    else {
        if (!hash_equals($_SESSION['token'], $_POST['token'])) {
                header("Location: ../");
        } 
    }
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header('Location: ../login.php');
	}
    require '../funcion.php';
    $consulta = new Consultas;
    if(isset($_GET['email'])) {
        $email = $consulta->escapar($_GET['email']);
        $consulta->preparar('DELETE FROM users WHERE email = ?', $email, 's');
    } elseif (isset($_POST['checkbox'])) {
         foreach ($_POST['checkbox'] as $email) {
            $consulta->preparar('DELETE FROM users WHERE email = ?', $email, 's');  
        }

    }
    $consulta->cerrar();
    header('Location: ../usuarios.php');


?>