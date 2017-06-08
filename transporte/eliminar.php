<?php
    session_start();
    

	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header("Location: ../login.php");
	}
    require '../funcion.php';
    $consulta = new Consultas;
    if(isset($_GET['transporte']) && isset($_GET['dominio'])) {
            $transporte = $consulta->escapar($_GET['transporte']);
            $dominio = $consulta->escapar($_GET['dominio']);
            $consulta->consulta("DELETE FROM transport WHERE domain = '$dominio' AND transport = '$transporte'");
    } else {
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>Algo ha fallado en la eliminación de transporte.</div>";
    }
    $consulta->cerrar();
    header('Location: ../transporte.php');


?>