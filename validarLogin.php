<?php 
	session_start();
	if((isset($_SESSION['id'])) && (!empty($_SESSION['id']))) { 
		header('Location: index.php');
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Inicio de sesión</title>
		
	</head>
	<body>
		<?php
			include 'funcion.php';
			$Conexion = new BD;
			$Consulta = new Consultas;
			$clave = $_POST['clave1'];
			$email = $Consulta->escapar($_POST['email']);
			$admin = $Consulta->preparar("SELECT email, password, bloqueado FROM admins WHERE email = ?", $email, 's');
			$resultado = $admin->get_result();
			$filas = $resultado->fetch_array(MYSQLI_NUM);
			$resultadoclave = print_r($filas[1], true);
			$estadocuenta = print_r($filas[2], true);
			// Lo ideal sería compartir las mismas contraseñas entre administrador y correo electrónico (para ello dejaría que Dovecot hiciese la comprobación de contraseñas con el comando exec y con una condición que compruebe que el comando envía la palabra "verified") pero como es demasiado trabajo que no puedo implementar por falta de tiempo lo que haré serán dos bases de datos: En una la autenticación la hará Dovecot, y en otra la hará PHP. Esto nos puede servir como una especie de fail-over (si algo ocurre y Dovecot falla podemos seguir entrando al modo administración, y viceversa podremos seguir enviando correo) y es mucho más fácil de implementar y menos laborioso
			// Variables para contar tanto los intentos como si ha sido un bloqueo temporal
			/* if(!isset($_SESSION['cuentaBloqueo'])) {
				$_SESSION['cuentaBloqueo'] = 4;
				$_SESSION['bloqueoPorContador'] = 0;
			} */
			$numfilas = $resultado->num_rows;
			if ($numfilas === 0 OR NULL) {
				$_SESSION['error'] = "El correo no está registrado.";
				$Conexion->cerrar();
				header('Location: login.php');
			} elseif (password_verify($clave, $resultadoclave) === FALSE ) {
				$Consulta->preparar("UPDATE sessions SET login_attempts = login_attempts + 1 WHERE email = ?", $email, 's');
				$_SESSION['error'] = "La contraseña introducida es incorrecta.";
				$Conexion->cerrar();
				header('Location: login.php');
				/* Esto es parte de una operación para bloquear conexiones tras x intentos que ya veré cómo implementar
				
				if($_SESSION['cuentaBloqueo'] > 0) {
					$_SESSION['cuentaBloqueo']= $_SESSION['cuentaBloqueo']-1;
					$variableLeible = $_SESSION['cuentaBloqueo'];
					$_SESSION['error'] = "La contraseña introducida es incorrecta. Le quedan $variableLeible intentos.";
					header('Location: login.php');
				} else{
					$actualizarestado = "UPDATE Usuarios SET bloqueado = '1' WHERE IDUsuario = '$resultadoid';";
					mysqli_query($conexion, $actualizarestado);
					$_SESSION['bloqueoPorContador'] = 1;
					$_SESSION['error'] = "Por motivos de seguridad su cuenta ha sido bloqueada temporalmente. Espere un poco antes de volver a intentar iniciar sesión.";
					 header('Location: login.php');
					$fecha = date("Y-m-d H:i:s");
					if(!isset($_SESSION['fechaBloqueo'])) {
						$_SESSION['fechaBloqueo'] = $fecha;
						$fechaDesbloqueo = date_add($fecha, date_interval_create_from_date_string('10 seconds'));;
						echo $fechaDesbloqueo;/*
						$_SESSION['fechaDesbloqueo'] = date("Y-m-d H:i:s", $fechaDesbloqueo);
							if($fecha >= $fechaDesbloqueo) {
							$_SESSION['cuentaBloqueo'] = 4;
							$_SESSION['bloqueoPorContador'] = 0;
							$_SESSION['error'] = ""; 
							$_SESSION['fechaBloqueo'] = "";
							$_SESSION['fechaDesbloqueo'] = "";
							$actualizarestado = "UPDATE Usuarios SET bloqueado = '0' WHERE IDUsuario = '$resultadoid';";
							mysqli_query($conexion, $actualizarestado);
							} */
					
				
	
							
							/* if($i==0) {
							break;
							
							/* $i = 3;
							$_SESSION['bloqueoPorContador'] = 0;
							$_SESSION['error'] = ""; 
							} else {
								header('Location: login.php');
							}
				} */
		/*	} elseif (($estadocuenta == '1') && ($_SESSION['bloqueoPorContador'] == 0)) {
				$_SESSION['error'] = "Su cuenta ha sido bloqueada. Por favor, contacte con un administrador lo antes posible.";
				header('Location: login.php');
			} elseif (($estadocuenta == '1') && ($_SESSION['bloqueoPorContador'] == 1)) {
				$_SESSION['error'] = "Por motivos de seguridad su cuenta ha sido bloqueada temporalmente. Espere un poco antes de volver a intentar iniciar sesión.";
				header('Location: login.php'); 
			*/} 
			
			else{
				$consulta_datos_usuario = $Consulta->preparar("SELECT name, surname, domain FROM users WHERE email = ?", $email, 's');
				$datos_usuario = $consulta_datos_usuario->get_result();
				$filas = $datos_usuario->fetch_array(MYSQLI_NUM);
				$_SESSION['email'] = $email;
				$_SESSION['nombre'] = print_r($filas[0], true);
				$_SESSION['apellidos'] = print_r($filas[1], true);
				$_SESSION['dominio'] = print_r($filas[2], true);
				$fecha = date('Y-m-d H:i:s');
				$Consulta->consulta("INSERT INTO sessions(email, last_login) VALUES ('{$email}', '{$fecha}')");
				$Consulta->cerrar();
				header('Location: index.php');
				}
			
			
		?>
	</body>
