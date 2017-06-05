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
            function nuevoDominio() {
                var nuevoCampo = "<td></td><td><div class=\"form-group\><form method=\"post\" autocomplete=\"off\"><input type=\"text\ name=\"dominio\" class=\"form-group\" maxLength=\"50\" placeholder=\"Dominio\"></div></td><td><button type=\"submit\" formaction=\"dominios/nuevo.php\" formmethod=\"post\" class=\"btn btn-scondary btn-sm\">A;adir</button></td>"
                document.getElementById("dominio").innerHTML = nuevoCampo;
            }
        </script>
        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-9 offset-md-2 pt-3">
                <label>No olvides a;adir un registro A y un registro MX en tu servidor DNS que apunte a este servidor de correo para que funcione en tu dominio. Un registro SPF no es necesario pero es recomendable para evitar el spam. <a href="https://mediatemple.net/community/products/dv/204404314/how-can-i-create-an-spf-record-for-my-domain">Lee más sobre cómo crear registros SPF aquí.</a></label>
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
                             <tr id="dominio"></tr>           
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3"> <button type="button" class="btn btn-secondary btn-sm" onclick="return nuevoDominio();"><i class="fa fa-plus"  aria-hidden="true"> </i> Añadir dominio </button></td></tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="submit" onclick="return confirm('NOTA: Eliminar un dominio eliminará los usuarios asociados a este. Está seguro?')" formmethod="post" id="boton" formaction="dominios/eliminar.php" class="btn btn-secondary btn-lg" disabled>Eliminar</button>
                    </form>
                </main>
            </div>
        </div>
    </body>
