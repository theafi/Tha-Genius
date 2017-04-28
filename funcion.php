<?php

function conectarBD() {

	$dbhost = "localhost"; //Los nombres de las variables son case-sensitive, no así las palabras reservadas
	$dbusuario = "proyecto";
	$dbpassword = "71JYNcXnIOejQZvk";
	$port = "3306";
	$conexion = mysqli_connect($dbhost . ":" . $port, $dbusuario, $dbpassword);
	if (!$conexion) die('Could not connect: ' . mysql_error($conexion));
	$creardb = "CREATE DATABASE IF NOT EXISTS rmi";
	mysqli_query($conexion, $creardb);
	$usardb = "use rmi";
	mysqli_query($conexion, $usardb);
	return $conexion;
	}
function subirImagen($i) {
	if(isset($_POST['submit'])) {		
		if (is_uploaded_file($_FILES['imagen']['tmp_name'][$i])) {
			$tipo = explode('/',$_FILES['imagen']['type'][$i]);
			if($_FILES['imagen']['error'][$i] > 0){
				die('Ha habido un error al subir la imagen.');
			} elseif($tipo[0] != "image"){
				die('Tipo de archivo no soportado o el archivo no es una imagen.');
			} elseif($_FILES['imagen']['size'][$i] > 1000000){
				die('El archivo excede el límite de tamaño.');
			} elseif(!getimagesize($_FILES['imagen']['tmp_name'][$i])){
					die('Asegúrese de que está subiendo una imagen');
			} else{
				$imagen[$i] = $_FILES['imagen']['name'][$i];
				$ruta[$i] = $_FILES['imagen']['tmp_name'][$i];
				if(isset($imagen[$i]) && !empty($imagen[$i])) {
					$localizacion = 'imagenes/'.date("Y-m-d-H-i-s")."-".$imagen[$i];
					if(!file_exists($localizacion)) {
						move_uploaded_file($ruta[$i], $localizacion);
					} else {
						die("Está intentando subir varios archivos con el mismo nombre o el mismo archivo varias veces. Por favor renombre los archivos y no abuse de la subida de imágenes.");
					}
				}
			
			}
		} else {
			$imagen[$i] = "";
		}		
	} return $imagen[$i];
}

function comprobarArray($array){ //Créditos a Henry
		function bucle($array, $prof=0){
				
			echo "<ul style='padding:10px; padding-left:30px; margin:10px; border-radius:5px;
					border:solid 1px; display:inline-block; vertical-align:top;'>";
			foreach($array as $cl => $vl){
				if (is_array($vl))  {
					echo "<li style='display:inline-block;margin-left:-20px;margin-right:20px'>
							<p style='text-align:center'><b>[$cl]</b></p>";
					bucle($vl, 1);
				}
				else  echo "<li>$cl: $vl";				
			}
			
			if ($prof){
				echo "<p ><strong>". count($array) .' elementos</strong></p>';
				echo "</ul>";
			}else{
				echo "</ul>";
				echo "<p style='margin-left:10px;'><strong>". count($array) .' elementos</strong></p>';
			}	
		}
		echo "<h4 style='padding-left:10px'>Mapeandor de array unidimensionales y multidimensionial</h4>";
		bucle($array);
	}
?>
