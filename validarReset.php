<?php
			session_start();
			if (empty($_SESSION['token']) || empty($_POST['token'])) { 
				header('Location: login.php');
			} elseif ($_SESSION['token'] !== $_POST['token']) {
                header('Location: login.php');
            } elseif((isset($_SESSION['email'])) && (!empty($_SESSION['email']))) {
				header('Location: index.php');
            } else {
                require 'funcion.php';
                $consulta = new Consultas;
                $contraseña = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $email = $consulta->escapar($_POST['email']);
                if (!$consulta->consulta("UPDATE admins SET password = '$contraseña' WHERE email = '$email'")) {
                    $_SESSION['error'] = "Algo ha fallado. Vuelva a intentarlo más tarde.";
                } else {
                    $_SESSION['exito'] = "Se ha reestablecido la contraseña con éxito.";
                }
                $consulta->cerrar();
                header('Location: login.php');
                
            }
?>