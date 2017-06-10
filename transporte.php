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
    if (isset($_SESSION['sessionexpire']) && ($_SESSION['sessionexpire'] >= time())) {
        header('Location: logout.php');
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
        <script src="js/funciones.js"></script>
        <script>
        // Aquí por alguna razón no puedo generar dinamicamente el campo de anadir (puedo hacerlo pero no me deja enviarlo despues) asi que lo he hecho permanente.
        </script>
        <title>Transporte</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-9 offset-md-2 pt-3">
                    <p>Puede definir uno o varios dominios para que utilicen una puerta de enlace SMTP diferente a la que este servidor usa. <strong>ESTO ES PARA USUARIOS AVANZADOS, NO TOQUE NADA SI NO SABE LO QUE ESTÁ HACIENDO O PODRIA POTENCIALMENTE QUEDARSE SIN SERVICIO.</strong></p>
                    <table>
                        <div id="error">
                            <?php if (isset($_SESSION['error'])){
                                echo $_SESSION['error'];
                                $_SESSION['error'] = "";
                            } else {
                                $_SESSION['error'] = "";
                            } ?>
                        </div>
                        <form action="eliminar.php" method="post">
                            <table class="table pt-2">
                                <thead>
                                    <tr><th></th><th>Dominio</th><th>Puerta de enlace</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $transporte = $consulta->consulta("SELECT domain, transport FROM transport");
                                        if ($transporte->num_rows === 0) {
                                            echo "<tr><td colspan=\"4\">No hay transportes personalizados designados en el sistema.</td></tr>";
                                        } else {
                                            while ($row = $transporte->fetch_array(MYSQLI_NUM)) {
                                            $row[0] = htmlspecialchars($row[0]); // Evito ataques XSS escapando caracteres prohibidos en HTML
                                            $row[1] = htmlspecialchars($row[1]);   
                                            echo "<tr><td></td><td>". $row[0]. "</td><td>". $row[1]. "</td><td><a href=\"transporte/eliminar.php?dominio={$row[0]}&transporte={$row[1]}\" title=\"Eliminar transporte\" onclick=\"return confirm('¿Está seguro de que desea borrar el transporte?')\"><i class=\"fa fa-times\" aria-hidden=\"true\"></a></i><td></tr>";        
                                            }
                                        }                                        
                                        
                                    ?>
                                    <tr>
                                        <form class="form-horizontal">
                                            <td></td>
                                            <td>
                                                
                                                    <select class="form-control" name="dominio" id="dominio">
                                                        <?php 
                                                            $consultaDominio = $consulta->consulta("SELECT domain FROM domains ORDER BY domain ASC"); 
                                                            while ($row = $consultaDominio->fetch_array(MYSQLI_NUM)) { 
                                                                $row[0] = htmlspecialchars($row[0]); 
                                                                $consultaTransporte = $consulta->preparar("SELECT domain FROM transport WHERE domain = ?", $row[0], 's');
                                                                $dominio = $consultaTransporte->fetch_array(MYSQLI_NUM); 
                                                                if ($dominio[0] !== $row[0]) {
                                                                    echo "<option>{$row[0]}</option>"; 
                                                                } else {
                                                                    echo "<option disabled>{$row[0]}</option>"; 
                                                                }
                                                            }
                                                        ?>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon">smtp:</span><input type="text" id="transporte" name="transporte" class="form-control" list="transporte" placeholder="IP o FQDN del servidor SMTP" required>
                                                </div>
                                                <input type="hidden" name="token" value="<?php echo $token; ?>" required></div></td>
                                                <td><button type="submit" formaction="transporte/nuevo.php" formmethod="post" class="btn btn-scondary btn-md">Añadir</button></td>
                                        </form>  
                                        
                                   
                                              
                                    </tr>
                                
                                </tbody>
                                <tfoot>
                                </tfoot>
                                
                            </table>
                           
                </main>
            </div>
        </div>
    </body>
