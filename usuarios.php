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

?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/estilo.css">

        <script src="https://use.fontawesome.com/ba338c7fda.js"></script>

        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
        <style>
        .table th, td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #eceeef;
            text-align: center;
        }
        </style>
        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
                    <form action="eliminar.php" method="post">
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
                                <tr><th></th><th>Correo electrónico</th><th>Nombre</th><th>Apellidos</th><th>Correo secundario</th><th>Opciones</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (isset($_GET['dominio'])) {
                                        $dominio = $consulta->escapar($_GET['dominio']);
                                        $usuarios = $consulta->preparar("SELECT users.email, users.name, users.surname, restore.alternate_email FROM users LEFT OUTER JOIN restore ON restore.user = users.email WHERE domain = ?", $dominio, 's');
                                        while ($row = $usuarios->fetch_array(MYSQLI_NUM)) {
                                            $row[0] = htmlspecialchars($row[0]);
                                            $row[1] = htmlspecialchars($row[1]);
                                            $row[2] = htmlspecialchars($row[2]);
                                            $row[3] = htmlspecialchars($row[3]);
                                            if ($row[0] === "do_not_reply@proyecto.net") { // Quiero conservar esta cuenta porque es la que usaré para recuperar contraseñas y enviar cualquier clase de información y por eso no dejo que nadie la toque 
                                                echo "<tr><td></td><td>". $row[0]. "</td><td>". $row[1]. "</td><td>". $row[2]. "</td><td>". $row[3]. "</td><td></td></tr>";                                    
                                            } else{
                                                echo "<tr><td><input type=\"checkbox\" class='form' onchange=\"document.getElementById('boton').disabled = !this.checked;\" value=\"{$row[0]}\" name=\"checkbox[]\" /> </td><td>";
                                                $consultaAdmin = $consulta->preparar("SELECT email, bloqueado FROM admins WHERE email = ?", $row[0], 's');
                                                $admin = $consultaAdmin->fetch_array(MYSQLI_NUM);
                                                if (!empty($admin[0])) {
                                                    echo "<a title=\"Este usuario es un administrador\"><i class=\"fa fa-user-o\" aria-hidden=\"true\"> </i> </a>";

                                                 }
                                                if ($admin[1] === 1) {
                                                    echo "<a title=\"Este usuario está bloqueado del panel de administración\"><i class=\"fa fa-ban\" aria-hidden=\"true\"></i> </a>";   
                                                 }
                                                 echo $row[0]."</td><td>". $row[1]. "</td><td>". $row[2]. "</td><td>". $row[3]. "</td><td><a href=\"modificarUsuario.php?email={$row[0]}\" title=\"Ajustes de usuario\"><i class=\"fa fa-cog\" aria-hidden=\"true\"></i></a> <a onclick=\"return confirm('Esto eliminará los usuarios. ¿Está seguro?')\" href=\"usuarios/eliminar.php?email={$row[0]}\" title=\"Eliminar usuario\"> <i class=\"fa fa-times\" aria-hidden=\"true\"></i></a></td></tr>";           
                                                 
                                                                                     
    
                                            }
                                        }
                                    } else {
                                        $usuarios = $consulta->consulta("SELECT users.email, users.name, users.surname, restore.alternate_email FROM users LEFT OUTER JOIN restore ON restore.user = users.email");
                                        while ($row = $usuarios->fetch_array(MYSQLI_NUM)) {
                                            $row[0] = htmlspecialchars($row[0]);
                                            $row[1] = htmlspecialchars($row[1]);
                                            $row[2] = htmlspecialchars($row[2]);
                                            $row[3] = htmlspecialchars($row[3]);
                                            if ($row[0] === "do_not_reply@proyecto.net") {
                                                echo "<tr><td></td><td>". $row[0]. "</td><td>". $row[1]. "</td><td>". $row[2]. "</td><td>". $row[3]. "</td><td></td></tr>";                                    
                                            } else{
                                                echo "<tr><td><input type=\"checkbox\" class='form' onchange=\"document.getElementById('boton').disabled = !this.checked;\" value=\"{$row[0]}\" name=\"checkbox[]\" /> </td><td>";
                                                $consultaAdmin = $consulta->preparar("SELECT email, bloqueado FROM admins WHERE email = ?", $row[0], 's');
                                                $admin = $consultaAdmin->fetch_array(MYSQLI_NUM);
                                                if (!empty($admin[0])) {
                                                    echo "<a title=\"Este usuario es un administrador\"><i class=\"fa fa-user-o\" aria-hidden=\"true\"> </i> </a>";
                                                 } 
                                                if ($admin[1] == 1) {
                                                    echo "<a title=\"Este usuario está bloqueado del panel de administración\"><i class=\"fa fa-ban\" aria-hidden=\"true\"></i> </a>";   
                                                 }
                                                 echo $row[0]."</td><td>". $row[1]. "</td><td>". $row[2]. "</td><td>". $row[3]. "</td><td><a href=\"modificarUsuario.php?email={$row[0]}\" title=\"Ajustes de usuario\"><i class=\"fa fa-cog\" aria-hidden=\"true\"></i></a> <a onclick=\"return confirm('Esto eliminará los usuarios. ¿Está seguro?')\" href=\"usuarios/eliminar.php?email={$row[0]}\" title=\"Eliminar usuario\"> <i class=\"fa fa-times\" aria-hidden=\"true\"></i></a></td></tr>";           
                                                 
                                            }
                                            
                                        }
                                    }
                                    
                                ?>

                            </tbody>
                            <tfoot>
                                <tr><td colspan="6"><a href="nuevoUsuario.php"> <i class="fa fa-plus" aria-hidden="true"> </i> Añadir usuario</a> </td></tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="submit" onclick="return confirm('Esto eliminará los usuarios. ¿Está seguro?')" formmethod="post" id="boton" formaction="usuarios/eliminar.php" class="btn btn-secondary btn-lg" disabled>Eliminar</button>
                    </form>
                </main>
            </div>
        </div>
    </body>
