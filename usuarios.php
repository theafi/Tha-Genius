<!DOCTYPE html>
<?php 
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
                        Dominio: 
                        <thead>
                            <tr><th>Correo electrónico</th><th>Nombre</th><th>Apellidos</th><th>Correo secundario</th></tr>
                        </thead>
                    </table>
                </main>
            </div>
        </div>
    </body>
