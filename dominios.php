<!DOCTYPE html>
<?php 
    session_start();
    require 'funcion.php';
	$token = $_SESSION['token'];
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header('Location: login.php');
	 }
	if (empty($_SESSION['token'])) { // Para evitar ataques CSRF
		$_SESSION['token'] = bin2hex(random_bytes(32));
	}
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

        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-5 offset-md-2 pt-3">
                    <form action="eliminar.php" method="post">
                        <table class="table">
                            <thead>
                                <tr><th></th><th>Dominio</th><th>Opciones</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                        $dominios = $consulta->consulta("SELECT domain FROM domains");
                                        while ($row = $dominios->fetch_array(MYSQLI_NUM)) {
                                            $row[0] = htmlspecialchars($row[0]);
                                                if ($row[0] === "proyecto.net"){
                                                    echo "<tr><td></td><td>{$row[0]}</td><td></td></tr>";
                                                } else {
                                                    echo "<tr><td><input type=\"checkbox\" class='form' onchange=\"document.getElementById('boton').disabled = !this.checked;\" value=\"{$row[0]}\" name=\"checkbox[]\" /> </td><td>". $row[0]. "</td><td><a href=\"usuarios/eliminar.php?email={$row[0]}\" title=\"Eliminar usuario\"> <i class=\"fa fa-times\" aria-hidden=\"true\"></i></a></td></tr>";           
                                                } 
                                                                                     
    
                                            }
                                ?>

                            </tbody>
                            <tfoot>
                                <tr><td colspan="3"><a href="nuevodominio.php"> <i class="fa fa-plus" aria-hidden="true"> </i> Añadir usuario</a> </td></tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="submit" formmethod="post" id="boton" formaction="dominios/eliminar.php" class="btn btn-secondary btn-lg" disabled>Eliminar</button>
                    </form>
                </main>
            </div>
        </div>
    </body>
