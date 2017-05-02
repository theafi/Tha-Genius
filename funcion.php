<?php
	class Conexion {
		// Variables protegidas para que no puedan ser accedidas fuera de las propias clases o sus clases heredadas
		protected static $dbhost = "mail.proyecto.net"; //Lo ideal es que tengamos que poner el nombre del dominio en lugar de la IP
		protected static $dbusuario = "php_admin";
		protected static $dbpassword = "wEXLx4d3p3MjaC3N";
		protected static $port = "3306";
		protected static $db = "mail";
		protected static $conexion;
		protected function conectarBD() { 
			self::$conexion = new mysqli(self::$dbhost, self::$dbusuario, self::$dbpassword, self::$db);
			if (self::$conexion === false) { // Muestra un error si falla la conexión
    			printf("Falló la conexión: %s\n", $conexion->connect_error);
    			exit();
			} else {
				echo "Conexión con éxito.";
				return self::$conexion;
			}
		}
		public function error() { //Recupera el último error de la base de datos
			$conexion = $this->conectarBD();
			return $conexion->error;
		}
		public function cerrar() { //Cierra la conexión a la base de datos cuando esta ha terminado
			$conexion = $this->conectarBD();
			return $conexion->close();
		} 
	}
	class Consulta extends Conexion {
		public function consultaLibre($consulta) { // Realiza cualquier consulta que escribas dentro. Útil para casos en las que las sentencias preparadas no sirven. Usar lo menos posible ya que puede ser susceptible a inyecciones SQL.
			//Conexión a la base de datos
			$conexion = parent::conectarBD();
			// Consulta a la base de datos
			$resultado = $conexion->query($consulta);
			return $resultado;
		}
		public function escapar($string) { // Escapa caracteres peligrosos de uno o varios valores que se vayan a introducir en una consulta (evita o mitiga el riesgo de inyección SQL)
			$conexion = parent::conectarBD();
			$string = $conexion->real_escape_string($string);
			return $string;
		}
		public function insertar($tabla, $datos, $formato) { // En la consulta se podrá insertar varios valores, separados por comas
			if (empty($tabla) || empty($datos)) { //Controlamos que ni la variable tabla ni la variable datos estén vacíos
				return false;
			}
			$conexion = parent::conectarBD();
			//Convertimos datos y formato en arrays
			$datos = array($datos);
			$formato = array($formato);

			//Construimos el string de formato
			$formato = implode('', $formato);
			$formato = str_replace('%', '', $formato);

			list($campos, $placeholders, $valores) = $this->prep_query($datos);
			array_unshift($valores, $formato);
			// Preparamos la consulta
			$sentencia = $conexion->prepare("INSERT INTO {$tabla} {{$campos}} VALUES {{$placeholders}}");
			// Llamamos de forma dinámica los bind values
			call_user_func_array(array($sentencia, 'bind_param'), $this->ref_values($valores));
			//Ejecutamos la sencencia
			$sentencia->execute();
			//Comprobamos que se ha insertado con éxito
			if ($sentencia->affected_rows) {
				return true;
			}
			return false;

		}
	}
	function pwssha512($password) { //Genera un hash Salted SHA512 (codificada en Base64) para almacenar contraseñas en Dovecot (Código por cortesía de https://mad9scientist.com/dovecot-password-creation-php/)
		$salt = substr(sha1(rand()), 0, 16);
		$hashedPassword = base64_encode(hash('sha512', $password . $salt, true) . $salt);
	}
		//Función vieja del otro proyecto, no muy útil de momento.
/* 	function subirImagen($i) {
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
	*/ 
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
