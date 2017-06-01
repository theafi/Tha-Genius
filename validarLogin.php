<?php 
	session_start();
	if((isset($_SESSION['id'])) && (!empty($_SESSION['id']))) { 
		header('Location: index.php');
	}
	if (!empty($_POST['token'])) {
		if (!hash_equals($_SESSION['token'], $_POST['token'])) {
			echo "ALERTA: Está sucediendo un ataque CSRF.";
		} 
	}	
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Inicio de sesión</title>
		
	</head>
	<body>
		<?php
			require 'funcion.php';
			$Conexion = new BD;
			$Consulta = new Consultas;
			$clave = $_POST['clave1'];
			$email = $Consulta->escapar($_POST['email']);
			$admin = $Consulta->preparar("SELECT email, password, bloqueado FROM admins WHERE email = ?", $email, 's');
			$filas = $admin->fetch_array(MYSQLI_NUM);
			$resultadoclave = print_r($filas[1], true);
			$estadocuenta = print_r($filas[2], true);
			$numfilas = $admin->num_rows;
			if ($numfilas === 0 OR NULL) {
				$_SESSION['error'] = "El correo no está registrado.";
				$Conexion->cerrar();
				header('Location: login.php');
			} elseif (password_verify($clave, $resultadoclave) === FALSE ) {
				$Consulta->preparar("UPDATE sessions SET login_attempts = login_attempts + 1 WHERE email = ?", $email, 's');
				$_SESSION['error'] = "La contraseña introducida es incorrecta.";
				$Conexion->cerrar();
				header('Location: login.php');
			} elseif (($estadocuenta == '1')) {
				$_SESSION['error'] = "Su cuenta ha sido bloqueada. Por favor, contacte con un administrador lo antes posible.";
				header('Location: login.php');
			}/* elseif (($estadocuenta == '1') && ($_SESSION['bloqueoPorContador'] == 1)) {
				$_SESSION['error'] = "Por motivos de seguridad su cuenta ha sido bloqueada temporalmente. Espere un poco antes de volver a intentar iniciar sesión.";
				header('Location: login.php'); 
				}
			*/ 
			
			else{
				$consulta_datos_usuario = $Consulta->preparar("SELECT name, surname, domain FROM users WHERE email = ?", $email, 's');
				$filas = $consulta_datos_usuario->fetch_array(MYSQLI_NUM);
				$_SESSION['email'] = $email;
				$_SESSION['nombre'] = print_r($filas[0], true);
				$_SESSION['apellidos'] = print_r($filas[1], true);
				$_SESSION['dominio'] = print_r($filas[2], true);
				$fecha = date('Y-m-d H:i:s');
				$Consulta->consulta("INSERT INTO sessions(email, last_login) VALUES ('{$email}', '{$fecha}')");
				$consulta_sessionid = $Consulta->consulta("SELECT session_id FROM sessions WHERE email = '{$email}' AND last_login = '{$fecha}'");
				$fetchsessionid = $consulta_sessionid->fetch_array(MYSQLI_NUM);
				$_SESSION['sessionid'] = print_r($fetchsessionid[0], true);
				$Consulta->cerrar();
				header('Location: index.php');
				}
			
			
		?>
	</body>
