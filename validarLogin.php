<?php 
	session_regenerate_id();
	session_id(mt_rand()); // mt_rand es mas rápido que rand()
	session_start();
	if((isset($_SESSION['sessionid'])) && (!empty($_SESSION['sessionid']))) { 
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
				$sessionid = session_id();
				$_SESSION['email'] = $email;
				$_SESSION['nombre'] = print_r($filas[0], true);
				$_SESSION['apellidos'] = print_r($filas[1], true);
				$_SESSION['dominio'] = print_r($filas[2], true);
				$_SESSION['sessionid'] = $sessionid;
				$fecha = date('Y-m-d H:i:s');
				$ip = $_SERVER['REMOTE_ADDR'];
				$proxyip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				$Consulta->consulta("INSERT INTO sessions(sessionid, email, current_login, ip, proxy_ip) VALUES ($sessionid, '$email', '$fecha', '$ip', '$proxy_ip')");
				$Consulta->cerrar();
				if (!isset($_POST['recuerdame'])) {
					$_SESSION['sessionexpire'] == time()+(60*60); // La sesión expirará después de una hora si no se marca Recuérdame.
				}
				header('Location: index.php');
				}
			
			
		?>
	</body>
