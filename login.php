<!DOCTYPE html>
<html>
	<head>
		<meta
		 charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
		<title>Iniciar sesión</title>
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
	session_start();
	if((isset($_SESSION['id'])) && (!empty($_SESSION['id']))) {
		header('Location: index.php');
	 }

	include 'funcion.php';
	$conexion = new BD; 
	$consulta = new Consultas;
	$admin = $consulta->consulta('SELECT * FROM admins');
	$resultadoadmin = $admin->fetch_array(MYSQLI_NUM);
	if (empty($resultadoadmin) || $resultadoadmin === FALSE) {
		$sentencia1 = "INSERT INTO `users` (`email`, `name`, `surname`, `password`, `alternate_email`) VALUES ('admin@proyecto.net', 'admin', 'admin', '{SSHA512}a260mcxkS1Mqt8hMhVrvllBOOk96WKJX7lutTihaYc2LX0MGUMa9Kvak/1KXYGWF9cOjM9ieD13vXgIw+N6luIdg4Tw=', NULL)";
		$consulta1= $consulta->consultaLibre($sentencia1);
		$sentencia2 = "INSERT INTO `admins` (`email`, `password`, `bloqueado`) VALUES ('admin@proyecto.net', '". password_hash("admin", PASSWORD_DEFAULT) ."', '0')";
		$consulta2 = $consulta->consultaLibre($sentencia2);
		echo "<div class=\"alert alert-warning\">
    <a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>
    <strong>ALERTA</strong> No se ha detectado una cuenta de administrador en el sistema. Se ha añadido una con las credenciales \"admin@proyecto.net\" y de contraseña \"admin\". Se recomienda encarecidamente cambiar las contraseñas de esta cuenta o deshabilitar/borrar la cuenta una vez hayas añadido una cuenta de administración.
</div>";
		$conexion->cerrar();	
	}  
?>

		<div class="login">
			<div class="form-group">
				<form action="validarLogin.php" autocomplete="on" method="post" enctype="multipart/form-data">
					<input type="email" class="form-control" name="email" placeholder="Correo electrónico" required>
			</div>
			<div class="form-group">
					<input type="password" class="form-control" name="clave1" id="clave1" minlength="5" maxlength="10" placeholder="Contraseña" required> <br>
			</div>
			<div class="form-group">
					<input type="checkbox" name="recuerdame"> Recuérdame
			</div>
			<button type="submit">Iniciar sesión</button> <a href="recuperar.html"><small>Olvidé mi contraseña</small></a><br>
			<small>¿No tiene cuenta? <a href="registro.php">Registre una cuenta</a></small>
			</form> 
			<div id="error"><?php 
                                if(!empty($_SESSION['error'])) {
                                    echo $_SESSION['error'];
                                    $_SESSION['error'] = "";
                                }
                            ?>
            </div>
		</div>
	</body>
</html>
