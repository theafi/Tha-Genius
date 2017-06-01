<?php
    session_start();
    require '../funcion.php';
    $consulta = new Consultas;
    if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		$consulta->cerrar();
        header('Location: login.php');
	 }
    if (!isset($_POST['token'])){
        $consulta->cerrar();
        header("Location: ../index.php");
    }
    if($_POST['token'] !== $_SESSION['token']) {
        $consulta->cerrar();
        header("Location: ../index.php");
    }

    $hash = new hash;
    $email = $consulta->escapar($_POST['email']);
    if (isset($_POST['password'])) {
        $contraseña = $hash->ssha512($_POST['password']);
        $consulta->consulta("UPDATE users SET password = '$contraseña' WHERE email = '$email'");
    } 
    if (isset($_POST['nombre'])) {
        $nombre = $consulta->escapar($_POST['nombre']);
        $consulta->consulta("UPDATE users SET name = '$nombre' WHERE email = '$email'");
    }
    if (isset($_POST['apellidos'])) {
        $apellidos = $consulta->escapar($_POST['apellidos']);
        $consulta->consulta("UPDATE users SET surname = '$apellidos' WHERE email = '$email'");
    }
    if (isset($_POST['respuesta'])) {
        $pregunta = $_POST['pregunta'];
        $respuesta = password_hash(strtoupper($_POST['respuesta']), PASSWORD_DEFAULT); // A las respuestas les doy el mismo tratamiento que a las contraseñas. Convierto el string en mayúsculas antes de hashearlo para que sea case insensitive 
        $consulta->consulta("UPDATE restore SET secretquestion = '$pregunta', answer = '$respuesta' WHERE user = '$email'");
    }
    if(isset($_POST['emailsecundario'])) {
            $emailsecundario = $consulta->escapar($_POST['emailsecundario']);
            $consulta->consulta("UPDATE restore SET alternate_email = '$emailsecundario' WHERE user = '$email'");

    }
   
    if (isset($_POST['seradmin']) && $_POST['seradmin'] === 'si') {
        $contraseñaadmin = password_hash($_POST['passwordadmin'], PASSWORD_DEFAULT);  
        $consulta->consulta("INSERT INTO admins (email, password) VALUES ('$email', '$contraseñaadmin')");
    } 
    if (isset($_POST['blockadmin']) && $_POST['blockadmin'] === 'si') {
        $consulta->preparar("UPDATE admins SET bloqueado = 1 WHERE email = ?", $email, 's');
    }
    if (isset($_POST['blockadmin']) && $_POST['blockadmin'] === 'no') {
        $consulta->preparar("UPDATE admins SET bloqueado = 0 WHERE email = ?", $email, 's');
    }
    if (isset($_POST['noadmin']) && $_POST['noadmin'] === 'si') {
        $consulta->preparar("DELETE FROM admins WHERE email = ?", $email, 's');
    }
    $consulta->cerrar();

    header("Location: ../usuarios.php");
    ?>