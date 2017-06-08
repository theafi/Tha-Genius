<?php
    session_start();
    

	if((!isset($_SESSION['email'])) && (empty($_SESSION['email']))) {
		header("Location: ../login.php");
	}
    require '../funcion.php';
    $consulta = new Consultas;
    if(isset($_GET['origen']) && isset($_GET['destino'])) {
            $origen = $consulta->escapar($_GET['origen']);
            $destino = $consulta->escapar($_GET['destino']);
            $consulta->consulta("DELETE FROM forwardings WHERE source = '$origen' AND destination = '$destino'");
    } else {
            $_SESSION['error'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Error: </strong>Algo ha fallado en la eliminación de redirecciones</div>";
    }
    $consulta->cerrar();
    header('Location: ../redirecciones.php');


?>