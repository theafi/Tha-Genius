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

?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/estilo.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
        <script src="js/pwstrength-bootstrap.js"></script>
        <script type="text/javascript">
           function contraseñaAleatoria() {
                var randomstring = btoa(Math.random()).slice(-10); // Genera números aleatoriamente y los convierte en un string en Base64. Conservo los 10 últimos caracteres del string únicamente
                document.getElementById("randompass").innerHTML = '<span class="input-group-btn"><button type="button" onclick="contraseñaAleatoria();" class="btn btn-sm" id="randompassword">Generar contraseña</button></span><input type="text" value="' + randomstring + '" class="form-control" readonly>';
            }
            function previsualizarEmail() { // Permite ver el email antes de que se cree
                var nombre = document.getElementById("usuario")
                var dominio = document.getElementById("dominio");
                document.getElementById("demo").value = nombre.value.replace(/[^A-Za-z0-9._%+-]/g, '') + '@' + dominio.options[dominio.selectedIndex].text;
            }
            $(document).ready(function () {
                var options = {};
                options.rules = {
                    activated: {
                        wordTwoCharacterClasses: true,
                        wordRepetitions: true
                    }
                };

                $(':password').pwstrength(options);
                
            });
        </script>
        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
                    <div class="col-md-4">
                        <form action="/usuarios/registro.php" method="post" autocomplete="off">  
                            <div class="form-group">
                                
                                <input type="email" class="form-control" oninput="previsualizarEmail(); this.value = this.value.replace(/[^A-Za-z0-9._%+-]/g, '')" name="usuario" id="usuario" maxLength="30" placeholder="Nombre de usuario" required>
                            </div>
                            <div class="form-group">
                                    <label>Dominio:
                                    <select class="form-control" oninput="previsualizarEmail();" name="dominio" id="dominio">
                                        <?php 
                                            $consulta = new Consultas;
                                            $dominios = $consulta->consulta("SELECT domain FROM domains");
                                            while ($row = $dominios->fetch_array(MYSQLI_NUM)) {
                                                echo "<option>{$row[0]}</option>";
                                            }
                                        ?>
                                    </select>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="email" id="demo" maxLength="80" placeholder="Correo electrónico previsualizado" disabled required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" minLength="6" required>  
                            </div>
                            <div class="col-sm-4 col-sm-offset-2" style="">
                                <div class="form-group">
                                    <div class="pwstrength_viewport_progress"></div>                        
                                </div>
                            </div>
                            <div class="input-group" id="randompass">
                                <span class="input-group-btn"><button type="button" onclick="contraseñaAleatoria();" class="btn btn-sm" id="randompassword">Generar contraseña</button></span>
                            </div>
                            <br>
                            <div class="input-group">
                                <input type="text" class="form-control" name="nombre" placeholder="Nombre (Opcional)">
                            </div>
                            <br>
                            <div class="input-group">
                                <input type="text" class="form-control" name="apellidos" placeholder="Apellidos (Opcional)">
                            </div>
                            <input type="hidden" name="token" value="<?php echo $token; ?>" required>
                            <br>
                            <div class="form-group"
                                <label>¿Hacer este usuario administrador? </label> 
                                <label class="radio-inline"><input type="radio" name="optradio" checked> No </label>
                                <label class="radio-inline"><input type="radio" name="optradio"> Sí </label>
                            </div>
                            <div class="btn-toolbar">
                                <span class="btn-toolbar"><a href="usuarios.php" class="btn btn-sm" role="button">Cancelar</a></span> <span class="btn-toolbar"><button type="submit" formaction="usuarios/registrar.php" class="btn btn-sm">Añadir usuario</button></span>
                            </div>
                            
                    </form>
                </div>
            </div>
         </main> 
      </body>