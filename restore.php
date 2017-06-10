<?php 
			session_start();
			if (empty($_SESSION['token']) || empty($_POST['token'])) { 
				header('Location: login.php');
			} elseif ($_SESSION['token'] !== $_POST['token']) {
                header('Location: login.php');
            } elseif((isset($_SESSION['email'])) && (!empty($_SESSION['email']))) {
				header('Location: index.php');
			}else {
                $_SESSION['restoretoken'] = bin2hex(random_bytes(32)); // Genero un nuevo token para la recuperación de contraseñas, así puedo hacer el enlace menos predecible y atacable.
                $_SESSION['alternativetoken'] = bin2hex(random_bytes(32)); // Genero otro token nuevo para la recuperación alternativa de contraseñas
                $_SESSION['tokentime'] =  time()+(5*60); // El tiempo que tengo hasta que los tokens sean válidos (5 mins)
                $restoretoken = $_SESSION['restoretoken'];
                $alternativetoken = $_SESSION['alternativetoken'];
                require 'funcion.php';
                require 'PHPMailer-master/PHPMailerAutoload.php';
                $consulta = new Consultas;
                $hash = new hash;
                $password = bin2hex(random_bytes(32)); // Genero una contraseña aleatoria cada vez que vaya a enviar un correo
                $passwordhash = $hash->ssha512($password);
                $consulta->consulta("REPLACE INTO users(email, domain, password) VALUES ('do_not_reply@proyecto.net', 'proyecto.net', '$passwordhash')"); // REPLACE funciona como INSERT pero si existe trunca lo que hay dentro.
                $email = $consulta->escapar($_POST['email']);
                //Conexión al servidor SMTP
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = 'mail.proyecto.net';
                $mail->SMTPAuth = true;
                $mail->Username = 'do_not_reply@proyecto.net';
                $mail->Password = $password;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->SMTPDebug = 2;
                //Opciones de PHPMailer
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true // Necesito esto para que funcione con mi certificado. En un entorno de producción no activaria esto ni borracho pero aquí tengo que hacerlo porque mi certificado esta firmado unicamente por mi mismo y no se puede validar.
                    )
                );
                $mail->setLanguage('es', '/PHPMailer-master/language/phpmailer.lang-es.php');
                $mail->CharSet = 'UTF-8';
                //Cabecera del correo
                $mail->setFrom('do_not_reply@proyecto.net', 'Sistema automático de recuperación de contraseñas');
                $mail->addAddress($email); 
                $mail->isHTML(true);  
                //Cuerpo del mensaje
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body    = "Si ha recibido este mensaje es porque ha pulsado en el formulario de recuperación de contraseña. De ser así, por favor <a href=\"proyecto.net/reset.php?email=$email&token=$restoretoken\">haga click aquí.</a> En caso contrario, por favor ignore este mensaje pues el enlace será inválido tras cinco minutos.";
                $mail->AltBody = 'Si ha recibido este mensaje es porque ha pulsado en el formulario de recuperación de contraseña. De ser así, por favor haga click aquí: proyecto.net/reset.php?email='.$email.'&token='.$restoretoken.' En caso contrario, por favor ignore este mensaje pues el enlace será inválido tras cinco minutos.'; // Cuerpo secundario para clientes que no soporten HTML
                if(!$mail->send()) {
                    $_SESSION['mensaje'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>Algo ha fallado: ". $mail->ErrorInfo."</div>";

                } else {
                    $_SESSION['mensaje'] = "<div class=\"alert alert-success\" role=\"alert\">Se ha enviado un mensaje de recuperación a tu correo. <a href=\"restore_alternative.php?email={$email}&token={$alternativetoken}\">No puedo acceder a mi correo</a></div>";
                }
                $consulta->cerrar();
                header('Location: recuperar.php');
            }



		?>