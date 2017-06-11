<?php
	class BD { 
		// Variables protegidas para que no puedan ser accedidas fuera de las propias clases o sus clases heredadas
		// Sé que es mala práctica poner los datos justo en los scripts pero lo hago para que me sea más facil debuggear
		protected static $dbhost = "mail.proyecto.net"; //Lo ideal es que tengamos que poner el nombre del dominio en lugar de la IP
		protected static $dbusuario = "php_admin";
		protected static $dbpassword = "wEXLx4d3p3MjaC3N";
		protected static $port = "3306";
		protected static $db = "mail";
		protected static $conexion;
		protected function conectar() { //Por razones de seguridad limito lo más posible el uso de la función conectar()
			self::$conexion = new mysqli(self::$dbhost, self::$dbusuario, self::$dbpassword, self::$db);
			if (self::$conexion === false) { // Muestra un error si falla la conexión
    			printf("Falló la conexión: %s\n", $conexion->connect_error);
    			exit();
			} else {
				return self::$conexion;
			}
		}
		public function error() { //Recupera el último error de la base de datos
			$conexion = $this->conectar();
			return $conexion->error;
		}
		public function cerrar() { //Cierra la conexión a la base de datos cuando esta ha terminado
			$conexion = $this->conectar();
			return $conexion->close();
		} 
	}
	class Consultas extends BD {
		public function consulta($sentencia) { // Realiza cualquier consulta que escribas dentro. Útil para casos en las que las sentencias preparadas no sirven. Usar lo menos posible ya que puede ser susceptible a inyecciones SQL.
			//Conexión a la base de datos
			$conexion = parent::conectar();
			// Consulta a la base de datos
			$resultado = $conexion->query($sentencia);
			return $resultado;
		}
		public function escapar($string) { // Escapa caracteres peligrosos de uno o varios valores que se vayan a introducir en una consulta (mitiga el riesgo de inyección SQL)
			$conexion = parent::conectar();
			$string = $conexion->real_escape_string($string);
			return $string;
		}
		public function preparar($sentencia, $datos, $formato) { // Para ver qué tipos se pueden introducir en la variable formato consulta la tabla types https://secure.php.net/manual/en/mysqli-stmt.bind-param.php
		/*
		TABLA DE DATOS DE LA VARIABLE $formato
		Character	Description
		i			corresponding variable has type integer
		d			corresponding variable has type doublef0
		s			corresponding variable has type string
		b			corresponding variable is a blob and will be sent in packets
		
		Las consultas preparadas tendrán la siguiente sintaxis:
		$consulta->preparar('SELECT * FROM email WHERE email = ?', 'prueba@proyecto.net', 's');
		A pesar de ser una función pública no uso la sintaxis $conexion::preparar() porque no es una función estática, y en PHP 7 llamar a un método no estático así está obsoleto
		*/
			$conexion = parent::conectar();
			if (!($stmt = $conexion->prepare($sentencia))) {
     			echo "La preparación ha fallado: (" . $stmt->errno() . ") " . $stmt->error();
			}
			if (!$stmt->bind_param($formato, $datos)) {
    			echo "La unión de parámetros ha fallado: (" . $stmt->errno() . ") " . $stmt->error();
			}
			if (!$stmt->execute()) {
    			echo "Ejecución fallida: (" . $stmt->errno() . ") " . $stmt->error();
			}
			//Fetch results
			return $stmt->get_result();
		}
		
	}
	class hash { // Una clase para generar hashes
		public function ssha512($password) { //Genera un hash Salted SHA512 (codificada en Base64) para almacenar contraseñas en Dovecot (Código por cortesía de https://mad9scientist.com/dovecot-password-creation-php/)
			$salt = substr(sha1(rand()), 0, 16);
			$hashedPassword = "{SSHA512}" . base64_encode(hash('sha512', $password . $salt, true) . $salt);
			return $hashedPassword;
		}
	}
?>
