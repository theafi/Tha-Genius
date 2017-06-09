<!DOCTYPE html>
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
			session_start();
			if (empty($_SESSION['token'])) { // Para evitar ataques CSRF
				$_SESSION['token'] = bin2hex(random_bytes(32));
			}
			$token = $_SESSION['token'];
			if((isset($_SESSION['email'])) && (!empty($_SESSION['email']))) {
				header('Location: index.php');
			}

			require 'funcion.php';
			$consulta = new Consultas;
		?>
		<div class="login">
				<div class="col-md-12">
					<form action="restore.php" autocomplete="on" method="post" enctype="multipart/form-data">
						<div class="input-group">
							<input type="email" class="form-control" name="email" placeholder="Correo electrónico" required>
						</div>
						<input type="hidden" name="token" value="<?php echo $token; ?>" required>
						<div class="form-group">
						</div>
						<button type="submit" class="btn btn-secondary btn-sm">Recuperar contrasena</button> 
					</form> 
					<br>
					<div id="mensaje">
						<?php 
							if(!empty($_SESSION['mensaje'])) {
									echo $_SESSION['mensaje'];
									$_SESSION['mensaje'] = "";
							}
						?>
					</div>
				</div>
					
					
				</div>
			</div>
		</div>
	
	</body>
</html>
