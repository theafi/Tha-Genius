<!DOCTYPE html>
<?php 
	session_start();
	if((isset($_SESSION['id'])) && (!empty($_SESSION['id']))) {
		header('Location: index.php');
	 }
?>
<html>
	<head>
		<meta charset="UTF-8">
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
