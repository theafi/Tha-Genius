<!DOCTYPE html>
<?php 
    session_start();
    require 'funcion.php';

	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header('Location: login.php');
	 }
	if (empty($_SESSION['token'])) { // Para evitar ataques CSRF
		$_SESSION['token'] = bin2hex(random_bytes(32));
	}
    $token = $_SESSION['token'];
    $consulta = new Consultas;
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/estilo.css">

        <script src="https://use.fontawesome.com/ba338c7fda.js"></script>

        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
        <script>
            function nuevaRedireccion() {
                var nuevoCampo = '<td></td><td><div class="col-md-4 offset-md-4"><form method="post" autocomplete="off"><input type="text" name="dominio" class="form-control" pattern="^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$" maxLength="50" placeholder="Dominio" required><input type="hidden" name="token" value="<?php echo $token; ?>" required></div></td><td><small><a href="#" onclick="return cerrarNuevo()">Cancelar</a> </small><button type="submit" formaction="dominios/nuevo.php" formmethod="post" class="btn btn-scondary btn-md">Añadir</button></td>'
                // La expresión regular "^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$" admite un nombre de dominio y un TLD (de 2 a 6 caracteres) como mínimo, y se le puede añadir otro TLD como pasa en algunos dominios, p.e: el TLD .co.uk
                document.getElementById("dominio").innerHTML = nuevoCampo
            }
            function cerrarRedireccion() {
                document.getElementById("dominio").innerHTML = ''
            }
        </script>
        <title>Dominios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-9 offset-md-2 pt-3">
                    <p>Redirige todo el tráfico que vaya hacia un correo electrónico a otro.</p>
                    <table>
                        <div id="error">
                            <?php if (isset($_SESSION['error'])){
                                echo $_SESSION['error'];
                                $_SESSION['error'] = "";
                            } else {
                                $_SESSION['error'] = "";
                            } ?>
                        </div>
                        <form action="redirecciones/eliminar.php" method="post">
                            <table class="table pt-2">
                                <thead>
                                    <tr><th></th><th>Origen</th><th>Destino</th><th></th></tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                        </form>
                    </table>
                </main>
            </div>
        </div>
    </body>
