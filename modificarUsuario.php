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
    $usuario = $consulta->escapar($_GET['email']);
    if (!$consultaUsuario = $consulta->preparar("SELECT email, name, surname FROM users WHERE email = ?", $usuario, 's')) {
        echo "ERROR: No se encuentra el usuario en la base de datos.";
    } else {
        $row = $consultaUsuario->fetch_array(MYSQLI_NUM);
    }
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
                    // Para que pueda enviar el formulario sin cambiar la contrase;a necesariamente tengo que permitir que si el valor es 0 que devuelva true
                    if (pass1.value.length === 0 && pass2.value.length === 0) {
                        return (true);
                    } else {
                        if(pass1.value.length < 6 && pass2.value.length < 6) {
                        pass1.style.backgroundColor = badColor;
                        pass2.style.backgroundColor = badColor;
                        message.style.color = badColor;
                        message.innerHTML = "La contraseña tiene que ser de 6 caracteres mínimo";
                        return (false);
                    } else {
                        return (true);
                    }
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
                        <form action="/usuarios/modificar.php" method="post" autocomplete="off" onSubmit="return checkPass(this);">  
                            <div class="form-group">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" maxLength="80" value="<?php echo $row[0]; ?>" placeholder="Correo electrónico" readonly required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" minLength="6">  
                            </div>
                            <div class="col-sm-4 col-sm-offset-2" style="">
                                <div class="form-group">
                                    <div class="pwstrength_viewport_progress"></div>                        
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="password" name="passwordcheck" class="form-control" id="passwordcheck" placeholder="Contraseña" minLength="6">  
                                <div id="error"></div>
                            </div>
                            
                            <div class="input-group" id="randompass">
                                <span class="input-group-btn"><button type="button" onclick="contraseñaAleatoria();" class="btn btn-sm" id="randompassword">Generar contraseña</button></span>
                            </div>
                            <br>
                            <div class="form-group">
                                <input type="text" class="form-control" name="nombre" value="<?php echo $row[1]; ?>" maxLength="50" placeholder="Nombre (Opcional)">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="apellidos" value="<?php echo $row[2]; ?>" maxLength="50" placeholder="Apellidos (Opcional)">
                            </div>
                            <div class="form-group">
                                    <label>Pregunta secreta:</label>
                                    <select class="form-control" name="pregunta">

                                        <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                                        <option value="¿A qué colegio de primaria fuiste?">¿A qué colegio de primaria fuiste?</option>
                                        <option value="¿Cuál es el nombre de tu superhéroe favorito?">¿Cuál es el nombre de tu superhéroe favorito?</option>
                                        <option value="¿Qué pasa cuando un objeto inamovible se cruza con una fuerza imparable?">¿Qué pasa cuando un objeto inamovible se cruza con una fuerza imparable?</option>
                                    </select>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="respuesta" maxLength="100" placeholder="Tu respuesta" >
                            </div>
                            <?php 
                                $consultaAdmins = $consulta->preparar("SELECT email, bloqueado FROM admins WHERE email = ?", $usuario, 's');
                                $admins = $consultaAdmins->fetch_array(MYSQLI_NUM);
                            ?>
                            <div class="form-group">
                                <input type="email" class="form-control" name="emailsecundario" value="<?php $consultaEmailSecundario = $consulta->preparar("SELECT alternate_email FROM restore WHERE user = ?", $usuario, 's'); $emailSecundario = $consultaEmailSecundario->fetch_array(MYSQLI_NUM); if (empty($emailSecundario[0])) { echo $emailSecundario[0]; } else {echo "";} ?>" maxLength="80" placeholder="Correo electrónico alternativo (Opcional)" >
                                <small>Se usará este correo para la recuperación de contraseñas en caso de establecerse.</small>
                            </div>
                            <input type="hidden" name="token" value="<?php echo $token; ?>" required>
                            <?php if (empty($admins[0])): ?>
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
                            <?php endif; 
                            if (!empty($admins[0])): ?> 
                                <label>Bloquear administrador?</label>
                                <div class="form-check form-check-inline">
                                 <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="blockadmin" id="inlineRadio1" value="no" checked> No
                                    </label>
                                    
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="blockadmin" id="inlineRadio2" value="si" onclick="opcionesDeAdministrador();"> Sí
                                    </label>
                                    </div>
                                    <br>
                                    <small>Un administrador bloqueado no podrá acceder a este portal, pero seguirá en la base de datos.</small>
                                    <br>
                                    <br>
                                    <label>Quitar permisos de administrador?</label>
                                    <div class="form-check form-check-inline">
                                 <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="noadmin" id="inlineRadio1" value="no" checked> No
                                    </label>
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="noadmin" id="inlineRadio2" value="si" onclick="opcionesDeAdministrador();"> Sí
                                    </label>
                                    </div>
                                    <br>
                                    <small>Esto eliminará por completo al administrador de la base de datos.</small>
                            <?php endif; ?>
                            <div class="btn-toolbar">
                                <span class="btn-toolbar"><a href="usuarios.php" class="btn btn-sm" role="button">Cancelar</a></span> <span class="btn-toolbar"><button type="submit" formaction="usuarios/modificar.php" class="btn btn-sm">Guardar modificaciones</button></span>
                            </div>
                            
                    </form>
                </div>
            </div>
         </main> 
      </body>