<!DOCTYPE html>
<?php 
    session_start();
    require 'funcion.php';
	$token = $_SESSION['token'];
	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header('Location: login.php');
	 }

    if (!empty($_POST['token'])) {
		if (!hash_equals($_SESSION['token'], $_POST['token'])) {
			header('Location: index.php');
		} 
	}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/estilo.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>

        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
                    <table class="table">
                        Dominio: <select class="form-control" onchange="location = this.options[this.selectedIndex].value;">
                            <option value="usuarios.php">Todos</option>
                            <?php 
                                $consulta = new Consultas;
                                $dominios = $consulta->consulta("SELECT domain FROM domains");
                                while ($row = $dominios->fetch_array(MYSQLI_NUM)) {
                                    echo "<option value=\"usuarios.php?dominio=".$row[0]."\">".$row[0]."</option>";
                                }  
                            ?>
                                </select> <br>
                        <thead>
                            <tr><th>Correo electrónico</th><th>Nombre</th><th>Apellidos</th><th>Correo secundario</th><th>Opciones</th></tr>
                        </thead>
                        <tbody>
                            <?php
                                if (isset($_GET['dominio'])) {
                                    $dominio = $_GET['dominio'];
                                    $usuarios = $consulta->preparar("SELECT email, name, surname, alternate_email FROM users WHERE domain = ?", $dominio, 's');
                                    while ($row = $usuarios->fetch_array(MYSQLI_NUM)) {
                                    echo "<tr><td>". $row[0]. "</td><td>". $row[1]. "</td><td>". $row[2]. "</td><td>". $row[3]. "</td><td><i class=\"fa fa-times\" aria-hidden=\"true\"></i></td></tr>";                                    }
                                } 
                                if (!isset($_GET['dominio']) || empty($_GET['dominio'])) {
                                    $usuarios = $consulta->consulta("SELECT email, name, surname, alternate_email FROM users");
                                    while ($row = $usuarios->fetch_array(MYSQLI_NUM)) {
                                        echo "<tr><td>". $row[0]. "</td><td>". $row[1]. "</td><td>". $row[2]. "</td><td>". $row[3]. "</td><td><i class=\"fa fa-times\" aria-hidden=\"true\"></i></td></tr>";
                                    }
                                }
                                
                            ?>
                        </tbody>
                    </table>
                </main>
            </div>
        </div>
    </body>
