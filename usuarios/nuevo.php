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
    // Saneo todos los datos del formulario 
    $email = $consulta->escapar($_POST['email']);
    $dominio = $consulta->escapar($_POST['dominio']);
    $contraseña = $hash->ssha512($_POST['password']);
    if (isset($_POST['nombre'])) {
        $nombre = $consulta->escapar($_POST['nombre']);
    }
    if (isset($_POST['apellidos'])) {
        $apellidos = $consulta->escapar($_POST['apellidos']);
    }
    $pregunta = $consulta->escapar($_POST['pregunta']); // Ya sé que parece una gilipollez sanear un select PERO NO ME FIO NI DE MI SOMBRA
    $respuesta = password_hash(strtoupper($_POST['respuesta']), PASSWORD_DEFAULT); // A las respuestas les doy el mismo tratamiento que a las contraseñas. Convierto el string en mayúsculas antes de hashearlo para que sea case insensitive 
    if(isset($_POST['emailsecundario'])) {
        $emailsecundario = $consulta->escapar($_POST['emailsecundario']);
    }
  
    if (isset($nombre)) {
        if (isset($apellidos)){
            $consulta->consulta("INSERT INTO users (email, domain, name, surname, password) VALUES ('$email', '$dominio', '$nombre', '$apellidos', '$contraseña')");
        } else {
            $consulta->consulta("INSERT INTO users (email, domain, name, password) VALUES ('$email', '$dominio', '$nombre', '$contraseña')");
        } 
    } else {
        if (isset($apellidos)){
            $consulta->consulta("INSERT INTO users (email, domain, surname, password) VALUES ('$email', '$dominio', '$apellidos', '$contraseña')");
        } else {
            $consulta->consulta("INSERT INTO users (email, domain, password) VALUES ('$email', '$dominio', '$contraseña')");
        } 
    }
    if (isset($emailsecundario)) {
        $consulta->consulta("INSERT INTO restore (user, alternate_email, secretquestion, answer) VALUES ('$email', '$emailsecundario', '$pregunta', '$respuesta')");
    } else {
        $consulta->consulta("INSERT INTO restore (user, secretquestion, answer) VALUES ('$email', '$pregunta', '$respuesta')");
    }
    if ($_POST['seradmin'] === 'si') {
        $contraseñaadmin = password_hash($_POST['passwordadmin'], PASSWORD_DEFAULT); // Convierto el string en mayúsculas antes de hashearlo para que sea case insensitive 
        $consulta->consulta("INSERT INTO admins (email, password) VALUES ('$email', '$contraseñaadmin')");
    }
    $consulta->cerrar();
    header("Location: ../usuarios.php");
    ?>