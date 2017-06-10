<!DOCTYPE html>
<?php
	session_start();
	if (empty($_SESSION['alternativetoken']) || empty($_GET['token'])): 
		header('Location: login.php');
	elseif ($_SESSION['alternativetoken'] !== $_GET['token']):
        header('Location: login.php');
    elseif((isset($_SESSION['email'])) && (!empty($_SESSION['email']))):
		header('Location: index.php');
	elseif(time() >= $_SESSION['tokentime']):
		$_SESSION['error'] = "El enlace ha caducado. Vuelva a intentarlo.";
		$_SESSION['alternativetoken'] = "";
		$_SESSION['tokentime'] = "";
		header('Location: login.php');
	else: 
		require 'funcion.php';
		$consulta = new Consultas;
		$email = $consulta->escapar($_GET['email']);
		$consultaAdmins = $consulta->consulta("SELECT alternate_email, secretquestion FROM restore WHERE user='$email'");
		if ($consultaAdmins->num_rows === 0):
			$_SESSION['mensaje'] = "Ha habido un error: Este usuario no existe o no tiene permisos de administrador. Vuelve a intentarlo más tarde.";
			$_SESSION['alternativetoken'] = "";
			$_SESSION['tokentime'] = "";
			header('Location: login.php');
		else:
			$admins = $consultaAdmins->fetch_array(MYSQLI_NUM);
			if (!empty($admins[0]) || is_null($admins[0])):
				$_SESSION['restoretoken'] = bin2hex(random_bytes(32)); // Genero un nuevo token para la recuperación de contraseñas, así puedo hacer el enlace menos predecible y atacable.
                $_SESSION['alternativetoken'] = bin2hex(random_bytes(32)); // Genero otro token nuevo para la recuperación alternativa de contraseñas
                $_SESSION['tokentime'] =  time()+(5*60); // El tiempo que tengo hasta que los tokens sean válidos (5 mins)
				$restoretoken = $_SESSION['restoretoken'];
                $alternativetoken = $_SESSION['alternativetoken'];
                require 'PHPMailer-master/PHPMailerAutoload.php';
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
                $mail->addAddress($admins[0]); 
                $mail->isHTML(true);  
                //Cuerpo del mensaje
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body    = "Si ha recibido este mensaje es porque ha pulsado en el formulario de recuperación de contraseña. De ser así, por favor <a href=\"proyecto.net/reset.php?email=$email&token=$restoretoken\">haga click aquí.</a> En caso contrario, por favor ignore este mensaje pues el enlace será inválido tras cinco minutos.";
                $mail->AltBody = 'Si ha recibido este mensaje es porque ha pulsado en el formulario de recuperación de contraseña. De ser así, por favor haga click aquí: proyecto.net/reset.php?email='.$email.'&token='.$restoretoken.' En caso contrario, por favor ignore este mensaje pues el enlace será inválido tras cinco minutos.'; // Cuerpo secundario para clientes que no soporten HTML
                if(!$mail->send()) {
                    $_SESSION['mensaje'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error </strong>Algo ha fallado: ". $mail->ErrorInfo."</div>";

                } else {
                    $_SESSION['mensaje'] = "<div class=\"alert alert-success\" role=\"alert\">Se ha enviado un mensaje de recuperación al correo secundario que definiste. <a href=\"restore_alternative.php?email={$email}&token={$alternativetoken}&noemail=yes\">No puedo acceder a mi correo secundario</a></div>";
                }
                $consulta->cerrar();
                header('Location: recuperar.php');
			elseif(empty($admins[0]) || $_GET['noemail'] === 'yes'):
?>
				<html>
					<head>
						<meta
						charset="UTF-8">
						<link rel="stylesheet" href="css/bootstrap.css">
						<script src="js/jquery.js"></script>
						<script src="js/bootstrap.js"></script>
						<title>Recuperar contraseña</title>
						<style>
							#error {
								color: red;	
							}
							.login {
								position: absolute;
								top: 50%;
								left: 50%;
								margin-right: -50%;
								transform: translate(-50%, -50%)
							}
							
						</style>
						
					</head>
					<body>
						<?php 
							$token = $_SESSION['alternativetoken'];
						?>
						<div class="login">
								<div class="col-md-12">
									<form action="reset.php" autocomplete="on" method="post" enctype="multipart/form-data">
										<div class="input-group">
										<label><?php 
												switch ($admins[1]){
													case 1:
														echo "¿Cuál es el nombre de tu primera mascota?";
														break;
													case 2:
														echo "¿A qué colegio de primaria fuiste?";
														break;
													case 3:
														echo "¿Cuál es el nombre de tu superhéroe favorito?";
														break;
													case 4:
														echo "¿Qué pasa cuando un objeto inamovible se cruza con una fuerza imparable?";
														break;
												}
											?></label>
										</div>
										<div class="input-group">
											<input type="text" class="form-control" name="respuesta" placeholder="Respuesta" required>
										</div>
										<input type="hidden" name="token" value="<?php echo $token; ?>" required>
										<input type="hidden" name="email" value="<?php echo $email; ?>" required>
										<div class="form-group">
										</div>
										<button type="submit" class="btn btn-secondary btn-sm">Recuperar contraseña</button> <a href="reset.php"><small>Cancelar</small></a>
									</form> 
									<br>
								</div>
						</div>
									
					
					
					</body>

				</html>
 <?php endif; 
 endif;
 endif;?>