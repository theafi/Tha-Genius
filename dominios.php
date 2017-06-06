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
                var nuevoCampo = '<td></td><td><div class="col-md-4 offset-md-4"><form method="post" autocomplete="off"><input type="text" name="dominio" class="form-control" pattern="^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$" maxLength="50" placeholder="Dominio" required><input type="hidden" name="token" value="<?php echo $token; ?>" required></div></td><td><small><a href="#" onclick="return cerrarNuevo()">Cancelar</a> </small><button type="submit" formaction="dominios/nuevo.php" formmethod="post" class="btn btn-scondary btn-md">Añadir</button></td>'
                // La expresión regular "^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$" admite un nombre de dominio y un TLD (de 2 a 6 caracteres) como mínimo, y se le puede añadir otro TLD como pasa en algunos dominios, p.e: el TLD .co.uk
                document.getElementById("dominio").innerHTML = nuevoCampo
            }
            function cerrarNuevo() {
                document.getElementById("dominio").innerHTML = ''
            }
        </script>
        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-9 offset-md-2 pt-3">
                    <label>No olvides añadir un registro A y un registro MX en tu servidor DNS que apunte a este servidor de correo para que funcione en tu dominio una vez añadido al sistema. Un registro SPF no es necesario pero es recomendable para evitar el spam. <a href="https://mediatemple.net/community/products/dv/204404314/how-can-i-create-an-spf-record-for-my-domain">Lee más sobre cómo crear registros SPF aquí.</a></label>
                    <br>
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
                                <tr><th></th><th>Dominio</th><th>Opciones</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                        $dominios = $consulta->consulta("SELECT domain, block FROM domains");
                                        while ($row = $dominios->fetch_array(MYSQLI_NUM)) {
                                            $row[0] = htmlspecialchars($row[0]); // Evito ataques XSS escapando caracteres prohibidos en HTML
                                                if ($row[0] === "proyecto.net"){
                                                    echo "<tr><td></td><td>{$row[0]}</td><td></td></tr>";
                                                } else {
                                                    if ($row[1] === '0') {
                                                        echo "<tr><td><input type=\"checkbox\" class='form' onchange=\"document.getElementById('boton').disabled = !this.checked\" value=\"{$row[0]}\" name=\"checkbox[]\" /> </td><td>". $row[0]. "</td><td><a href=\"dominios/eliminar.php?dominio={$row[0]}\" title=\"Eliminar dominio\" onclick=\"return confirm('ALERTA: Borrar el dominio borrará los usuarios asociados a él. ¿Está seguro?')\"> <i class=\"fa fa-times\" aria-hidden=\"true\"></i></a> <a href=\"dominios/bloquear.php?dominio={$row[0]}\" title=\"Bloquear dominio\" onclick=\"return confirm('NOTA: Bloquear el dominio impedirá crear nuevos usuarios en este dominio e impedirá que usuarios creados en este dominio que no hayan iniciado sesión previamente puedan enviar correo, pero no impedirá que usuarios activos en este dominio puedan seguir enviando y recibiendo correo. Está seguro de que desea proceder?')\"><i class=\"fa fa-lock\" aria-hidden=\"true\"></i></a></td></tr>";
                                                    } else {
                                                        echo "<tr><td><input type=\"checkbox\" class='form' onchange=\"document.getElementById('boton').disabled = !this.checked\" value=\"{$row[0]}\" name=\"checkbox[]\" /> </td><td><i class=\"fa fa-ban\" aria-hidden=\"true\" title=\"Dominio bloqueado\"> </i> ". $row[0]. "</td><td><a href=\"dominios/eliminar.php?dominio={$row[0]}\" title=\"Eliminar dominio\" onclick=\"return confirm('ALERTA: Borrar el dominio borrará los usuarios asociados a él. ¿Está seguro?')\"> <i class=\"fa fa-times\" aria-hidden=\"true\"></i></a> <a title=\"Desbloquear dominio\" href=\"dominios/bloquear.php?dominio={$row[0]}\" ><i class=\"fa fa-unlock-alt\" aria-hidden=\"true\"></i> </a></td></tr>";
                                                    }
                                                                   
                                                }                                        
                                        }
                                ?>
                             <tr id="dominio"></tr>           
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3"> <button type="button" class="btn btn-secondary btn-md" onclick="return nuevoDominio()"><i class="fa fa-plus"  aria-hidden="true"> </i> Añadir dominio </button></td></tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="submit" onclick="return confirm('NOTA: Eliminar un dominio eliminará los usuarios asociados a este. Está seguro?')" formmethod="post" id="boton" formaction="dominios/eliminar.php" class="btn btn-secondary btn-md" disabled>Eliminar</button>
                    </form>

                </main>
            </div>
        </div>
    </body>
