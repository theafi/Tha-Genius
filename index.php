<?php
    session_start();
	if (empty($_SESSION['token'])) { // Para evitar ataques CSRF
		$_SESSION['token'] = bin2hex(random_bytes(32));
	}
	$token = $_SESSION['token'];
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header('Location: login.php');
	 }
    if (isset($_SESSION['sessionexpire']) && ($_SESSION['sessionexpire'] <= time())) {
        header('Location: logout.php');
    }

     require 'funcion.php';
     $consulta = new Consultas;
     if (!(isset($_GET['section']))) {
         $_GET['section'] = 'Inicio';
     }
     $email = htmlspecialchars($_SESSION['email']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/estilo.css">
        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
        <title>Portal de administración</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
                        <h1>Inicio</h1>
                        <p>Bienvenido <?php if (isset($_SESSION['nombre'])): echo htmlspecialchars($_SESSION['nombre'])." "; if(isset($_SESSION['apellidos'])): echo htmlspecialchars($_SESSION['apellidos']); endif; else: echo $email; endif; echo "."; ?> <p>
                        <p>Su último inicio de sesión fue el
                            <?php
                                $consultaUltimaSesion = $consulta->preparar("SELECT last_login FROM sessions WHERE email = ? ORDER BY last_login ASC LIMIT 2, 2", $email, 's');
                                $row = $consultaUltimaSesion->fetch_array(MYSQLI_NUM);
                                echo $row[0];
                                
                            ?> </p>
                        <h1>Estadísticas</h1>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <thead>
                                </thead>
                                <tbody>
                                    <tr><th>Usuarios registrados</th><td><?php $cuentaUsers = $consulta->consulta("SELECT COUNT(*) FROM users"); $cuenta = $cuentaUsers->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Dominios registrados</th><td><?php $cuentaDominios = $consulta->consulta("SELECT COUNT(*) FROM domains"); $cuenta = $cuentaDominios->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Número de administradores</th><td><?php $cuentas = $consulta->consulta("SELECT COUNT(*) FROM admins"); $cuenta = $cuentas->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Redirecciones definidas</th><td><?php $cuentas = $consulta->consulta("SELECT COUNT(*) FROM forwardings"); $cuenta = $cuentas->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Dominios con distinta puerta de enlace</th><td><?php $cuentas = $consulta->consulta("SELECT COUNT(*) FROM transport"); $cuenta = $cuentas->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Administradores bloqueados</th><td><?php $cuentas = $consulta->consulta("SELECT COUNT(*) FROM admins WHERE bloqueado = 1"); $cuenta = $cuentas->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Dominios bloqueados</th><td><?php $cuentas = $consulta->consulta("SELECT COUNT(*) FROM domains WHERE block = 1"); $cuenta = $cuentas->fetch_array(MYSQLI_NUM); echo $cuenta[0]; ?></td></tr>
                                    <tr><th>Usuario con más inicios de sesion</th><td><?php $cuentas = $consulta->consulta("SELECT email, count(*) FROM sessions GROUP BY email"); $cuenta = $cuentas->fetch_array(MYSQLI_NUM); echo $cuenta[0]." (".$cuenta[1].")"; ?></td></tr>

                                </tbody>
                            </table>
                        </div>
                </main>
            </div>
        </div>
    </body>
</html>