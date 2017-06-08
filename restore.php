<?php 
			session_start();
			if (empty($_SESSION['token']) || empty($_POST['token'])) { // Para evitar ataques CSRF
				header('Location: login.php');
			} elseif ($_SESSION['token'] !== $_POST['token']) {
                header('Location: login.php');
            } elseif((isset($_SESSION['email'])) && (!empty($_SESSION['email']))) {
				header('Location: index.php');
			}else {
                require 'funcion.php';
                require 'PHPMailer-master/PHPMailerAutoload.php';
                $consulta = new Consultas;
                $hash = new hash;
                $password = $hash->ssha512(bin2hex(random_bytes(32))); // Genero una contrasena aleatoria cada vez que vaya a enviar un correo
                $consulta->consulta("REPLACE INTO users(email, domain, password) VALUES ('do_not_reply@proyecto.net', 'proyecto.net', '$password')");
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = 'mail.proyecto.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'do_not_reply@proyecto.net';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $consultaAdmins = $consulta->
            }



		?>