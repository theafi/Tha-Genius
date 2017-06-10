<!DOCTYPE html>
<?php
	session_start();
	if (isset($_GET['token']) && empty($_GET['token'])): 
		if (!isset($_POST['token'])):
			header('Location: login.php');
			endif;
	elseif (isset($_GET['token']) && $_SESSION['restoretoken'] !== $_GET['token']):
		if ($_SESSION['alternativetoken'] !== $_POST['token']):
        	header('Location: login.php');
			endif;
    elseif((isset($_SESSION['email'])) && (!empty($_SESSION['email']))):
		header('Location: index.php');
	elseif(time() >= $_SESSION['tokentime']):
		$_SESSION['error'] = "El enlace ha caducado. Vuelva a intentarlo.";
		$_SESSION['restoretoken'] = "";
		$_SESSION['tokentime'] = "";
		header('Location: login.php');
	else: 
		require 'funcion.php';
		$consulta = new Consultas;
		if (isset($_GET['email'])){
			$email = $consulta->escapar($_GET['email']);
		} elseif (isset($_POST['email'])){
			$email = $consulta->escapar($_POST['email']);
		}
		
		$consultaAdmins = $consulta->consulta("SELECT answer FROM restore WHERE user='$email'");
		if ($consultaAdmins->num_rows === 0):
			$_SESSION['mensaje'] = "Ha habido un error: Este usuario no existe o no tiene permisos de administrador. Vuelve a intentarlo más tarde.";
			$_SESSION['restoretoken'] = "";
			$_SESSION['tokentime'] = "";
			header('Location: login.php');
		else:
			$admins = $consultaAdmins->fetch_array(MYSQLI_NUM);
			if (!empty($admins[0]) || is_null($admins[0])):
				$restoretoken = $_SESSION['restoretoken'];
				if(isset($_POST['respuesta'])) :
					$alternativetoken = $_POST['token'];
					$respuesta = strtoupper($_POST['respuesta']);
					if (!password_verify($respuesta, $admins[0])) :
						$_SESSION['mensaje'] = "La respuesta no es válida.";
						header('Location: restore_alternative.php?email=$email&token=$alternativetoken&noemail=yes');
					endif;
				endif;
?>
				<html>
					<head>
						<meta charset="UTF-8">
						<link rel="stylesheet" href="css/bootstrap.css">
						<script src="js/jquery.js"></script>
						<script src="js/bootstrap.js"></script>
						<script src="js/funciones.js"></script>
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
							if (empty($_SESSION['token'])) { // Para evitar ataques CSRF
								$_SESSION['token'] = bin2hex(random_bytes(32));
							}
							$token = $_SESSION['token'];
						?>
						<div class="login">
								<div class="col-md-12">
									<form class="form-control" action="validarReset.php" onsubmit="return checkPass(this)" autocomplete="on" method="post" enctype="multipart/form-data">
										<div class="input-group">
											<input type="password" class="form-control" name="password" minLength="6" placeholder="Nueva contraseña" required>
										</div>
										<div class="input-group">
											<input type="password" class="form-control" name="passwordcheck" minLength="6" placeholder="Vuelva a introducir su contraseña" required>
										</div>
										<input type="hidden" name="token" value="<?php echo $token; ?>" required>
										<input type="hidden" name="email" value="<?php echo $email; ?>" required>
										<div class="form-group">
										</div>
										<button type="submit" class="btn btn-secondary btn-sm">Reestablecer contraseña</button> <a href="login.php"><small>Cancelar</small></a>
									</form> 
									<br>
									<div id="error">
									</div>
								</div>
						</div>
									
					
					
					</body>

				</html>
 <?php endif; 
 endif;
 endif;?>