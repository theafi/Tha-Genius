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
        <style>
        .btn-toolbar {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: start;
            -webkit-justify-content: flex-start;
            -ms-flex-pack: start;
            justify-content: flex-start;
            padding-bottom: 40px;
        }
        </style>
        <script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
        <script src="js/pwstrength-bootstrap.js"></script>
        <script src="js/funciones.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                var options = {};
                options.rules = {
                    activated: {
                        wordTwoCharacterClasses: true,
                        wordRepetitions: true
                    }
                };

                $('#password').pwstrength(options);
                
            });
        </script>
        <title>Usuarios</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <?php include 'sidebar.php'; ?>
                <main class="col-sm-9 offset-sm-3 col-md-4 offset-md-5 pt-4">
                        <form action="/usuarios/nuevo.php" method="post" autocomplete="off" onSubmit="return checkPass(this);">  
                            <div class="form-group">
                                <input type="text" class="form-control" oninput="previsualizarEmail(); this.value = this.value.replace(/[^A-Za-z0-9._%+-]/g, '')" name="usuario" id="usuario" maxLength="30" placeholder="Nombre de usuario" required>
                            </div>
                            <div class="form-group">
                                    <label>Dominio:</label>
                                    <select class="form-control" oninput="previsualizarEmail();" name="dominio" id="dominio">
                                        <?php 
                                            $consulta = new Consultas;
                                            $dominios = $consulta->consulta("SELECT domain, block FROM domains");
                                            while ($row = $dominios->fetch_array(MYSQLI_NUM)) {
                                                if ($row[1] === '1') {
                                                    echo "<option value=\"{$row[0]}\"disabled>{$row[0]} ---BLOQUEADO---</option>"; //Los dominios bloqueados no serán seleccionables
                                                } else {
                                                    echo "<option>{$row[0]}</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" id="demo" maxLength="80" placeholder="Correo electrónico previsualizado" readonly required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" minLength="6" required>  
                            </div>
                            <div class="col-sm-4 col-sm-offset-2" style="">
                                <div class="form-group">
                                    <div class="pwstrength_viewport_progress"></div>                        
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="password" name="passwordcheck" class="form-control" id="passwordcheck" placeholder="Contraseña" minLength="6" required>  
                                <div id="error"></div>
                            </div>
                            
                            <div class="input-group" id="randompass">
                                <span class="input-group-btn"><button type="button" onclick="contraseñaAleatoria();" class="btn btn-sm" id="randompassword">Generar contraseña</button></span>
                            </div>
                            <br>
                            <div class="form-group">
                                <input type="text" class="form-control" name="nombre" maxLength="50" placeholder="Nombre (Opcional)">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="apellidos" maxLength="50" placeholder="Apellidos (Opcional)">
                            </div>
                            <div class="form-group">
                                    <label>Pregunta secreta:</label>
                                    <select class="form-control" name="pregunta">
                                        <option value="1">¿Cuál es el nombre de tu primera mascota?</option>
                                        <option value="2">¿A qué colegio de primaria fuiste?</option>
                                        <option value="3">¿Cuál es el nombre de tu superhéroe favorito?</option>
                                        <option value="4">¿Qué pasa cuando un objeto inamovible se cruza con una fuerza imparable?</option>
                                    </select>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="respuesta" maxLength="100" placeholder="Tu respuesta" required>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" name="emailsecundario" maxLength="80" placeholder="Correo electrónico alternativo (Opcional)" >
                                <small>Se usará este correo para la recuperación de contraseñas en caso de establecerse.</small>
                            </div>
                            <input type="hidden" name="token" value="<?php echo $token; ?>" required>
                            <div class="form-check form-check-inline">
                                <label>¿Hacer este usuario administrador?</label> 
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="seradmin" id="inlineRadio1" value="no" onclick='document.getElementById("admin").innerHTML = "";' checked> No
                                </label>
                                </div>
                                <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="seradmin" id="inlineRadio2" value="si" onclick="opcionesDeAdministrador();"> Sí
                                </label>
                            </div>
                            <div id="admin">

                            </div>
                            <div class="btn-toolbar">
                                <span class="btn-toolbar"><a href="usuarios.php" class="btn btn-sm" role="button">Cancelar</a></span> <span class="btn-toolbar"><button type="submit" formaction="usuarios/nuevo.php" class="btn btn-sm">Añadir usuario</button></span>
                            </div>
                            
                    </form>
                </div>
            </div>
         </main> 
      </body>