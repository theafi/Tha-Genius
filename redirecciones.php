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
        <script src="js/funciones.js"></script>
        <script>

            function nuevaRedireccion() { 
                
            } // Por más que lo intente no puedo deshabilitar los correos que ya han sido seleccionados, así que voy a hacer la comprobación en el servidor y pasando

        </script>
        <title>Redirecciones</title>
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
                        
                            <table class="table pt-2">
                                <thead>
                                    <tr><th></th><th>Origen</th><th>Destino</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $forwardings = $consulta->consulta("SELECT source, destination FROM forwardings");
                                        if ($forwardings->num_rows === 0) {
                                            echo "<tr><td colspan=\"4\">No hay redirecciones designadas en el sistema.</td></tr>";
                                        } else {
                                            while ($row = $forwardings->fetch_array(MYSQLI_NUM)) {
                                            $row[0] = htmlspecialchars($row[0]); // Evito ataques XSS escapando caracteres prohibidos en HTML
                                            $row[1] = htmlspecialchars($row[1]);   
                                            echo "<tr><td><input type=\"checkbox\" class='form' onchange=\"document.getElementById('boton').disabled = !this.checked\" value=\"{$row[0]}\" name=\"checkbox[]\" /> </td><td>". $row[0]. "</td><td>". $row[1]. "</td><td><a href=\"redirecciones/eliminar.php?origen={$row[0]}&destino={$row[1]}&token={$token}\" title=\"Eliminar redirección\" onclick=\"return confirm('¿Está seguro de que desea borrar la redirección?')\"><i class=\"fa fa-times\" aria-hidden=\"true\"></a></i><td></tr>";        
                                            }
                                        }                                        
                                        
                                    ?>
                                    <tr>
                                    <div id="nuevoForwarding" style="display: none;">
                                        <form>
                                            <td></td>
                                            <td>
                                                <div class="col-md-4 offset-md-4">
                                                    <select class="form-control" name="origen" id="origen">
                                                        <?php 
                                                            $consultaUsuarios = $consulta->consulta("SELECT email FROM users ORDER BY domain ASC"); 
                                                            while ($row = $consultaUsuarios->fetch_array(MYSQLI_NUM)) { 
                                                                $row[0] = htmlspecialchars($row[0]); echo "<option>{$row[0]}</option>"; } 
                                                        ?>
                                            </td>
                                            <td>
                                                <input type="email" name="destino" class="form-control" list="destino" placeholder="Doble click para ver sugerencias" required multiple>
                                                <datalist id="destino">
                                                    <?php 
                                                        $consultaUsuarios = $consulta->consulta("SELECT email FROM users ORDER BY domain ASC"); 
                                                        while ($row = $consultaUsuarios->fetch_array(MYSQLI_NUM)) {
                                                            $row[0] = htmlspecialchars($row[0]); 
                                                            echo "<option>{$row[0]}</option>"; 
                                                        }
                                                    ?> 
                                                </datalist>
                                                <input type="hidden" name="token" value="<?php echo $token; ?>" required></div></td>
                                                <td><button type="submit" formaction="redirecciones/nuevo.php" formmethod="post" class="btn btn-scondary btn-md">Añadir</button></td>
                                        </form>  
                                    </div>
                                              
                                    </tr>
                                
                                </tbody>
                                <tfoot>
                                    <tr><td colspan="4"> <button type="button" class="btn btn-secondary btn-md" onclick="return nuevaRedireccion()"><i class="fa fa-plus"  aria-hidden="true"> </i> Añadir dominio </button></td></tr>
                                </tfoot>
                            </table>
                </main>
            </div>
        </div>
    </body>
