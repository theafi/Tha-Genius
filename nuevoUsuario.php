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
        <script type="text/javascript">
            function checkPass(f) {
            
                //Store the password field objects into variables ...
                var pass1 = f.elements["password"];
                var pass2 = f.elements["passwordcheck"];
                //Store the Confimation Message Object ...
                var message = document.getElementById('error');
                //Set the colors we will be using ...3.
                var badColor = "#ff6666";
                //Compare the values in the password field 
                //and the confirmation field
                if(pass1.value == pass2.value){
                    //Las contraseñas coinciden
                    if(pass1.value.length < 6 && pass2.value.length < 6) {
                        pass1.style.backgroundColor = badColor;
                        pass2.style.backgroundColor = badColor;
                        message.style.color = badColor;
                        message.innerHTML = "La contraseña tiene que ser de 6 caracteres mínimo";
                        return (false);
                    } else {
                        return (true);
                    }
                } else{
                    //The passwords do not match.
                    //Set the color to the bad color and
                    //notify the user.
                    pass2.style.backgroundColor = badColor;
                    message.style.color = badColor;
                    message.innerHTML = "Las contraseñas no coinciden";
                    return (false);
                }
        }  
           function contraseñaAleatoria() {
                var randomstring = btoa(Math.random()).slice(-10); // Genera números aleatoriamente y los convierte en un string en Base64. Conservo los 10 últimos caracteres del string únicamente para generar una contraseña fuerte.
                document.getElementById("randompass").innerHTML = '<span class="input-group-btn"><button type="button" onclick="contraseñaAleatoria();" class="btn btn-sm" id="randompassword">Generar contraseña</button></span><input type="text" value="' + randomstring + '" class="form-control" readonly>';
            }
            function previsualizarEmail() { // Permite ver el email antes de que se cree
                var nombre = document.getElementById("usuario")
                var dominio = document.getElementById("dominio");
                document.getElementById("demo").value = nombre.value.replace(/[^A-Za-z0-9._%+-]/g, '') + '@' + dominio.options[dominio.selectedIndex].text;
            }
            function opcionesDeAdministrador() {
                var codigoHTML = '<div class="form-group">'
                               + '<input type="password" name="passwordadmin" class="form-control" id="passwordadmin" placeholder="Contraseña para el administrador" minLength="6" required>  '
                            +'<small>Es aconsejable utilizar una contraseña distinta a la anterior</small>'
                            +'</div>'
                           + '<div class="col-sm-4 col-sm-offset-2" style="">'
                               + '<div class="form-group">'
                                   + '<div class="pwstrength_viewport_progress"></div> '                       
                                +'</div>'
                         +  ' </div>';
                document.getElementById("admin").innerHTML = codigoHTML;
            }
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
                <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
                    <div class="col-md-5">
                        <form action="/usuarios/nuevo.php" method="post" autocomplete="off" onSubmit="return checkPass(this);">  
                            <div class="form-group">
                                <input type="text" class="form-control" oninput="previsualizarEmail(); this.value = this.value.replace(/[^A-Za-z0-9._%+-]/g, '')" name="usuario" id="usuario" maxLength="30" placeholder="Nombre de usuario" required>
                            </div>
                            <div class="form-group">
                                    <label>Dominio:</label>
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
                                        <option>¿Cuál es el nombre de tu primera mascota?</option>
                                        <option>¿A qué colegio de primaria fuiste?</option>
                                        <option>¿Cuál es el nombre de tu superhéroe favorito?</option>
                                        <option>¿Qué pasa cuando un objeto inamovible se cruza con una fuerza imparable?</option>
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