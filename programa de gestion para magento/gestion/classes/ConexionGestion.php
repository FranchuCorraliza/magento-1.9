<?php

	class ConexionGestion {
        private $server="localhost";
		private $usuario="gestion";
		private $password="Elite2017.";
		private $dataBase="gestion";
        
        public function conexion(){
            $conn = new PDO("mysql:host=$this->server;dbname=$this->dataBase", $this->usuario, $this->password);
            return $conn;
        }
        public function obtenerresultados($query){
            $conexion = $this->conexion();
            $stmt = $conexion->prepare($query); 
			$stmt->execute();
			return $stmt->fetchall(PDO::FETCH_BOTH);
        }
        public function obtenerusuario($campo, $texto, $password){
            $query ="SELECT * FROM `usuarios` WHERE `" . strtoupper ($campo) . "`='" . $texto . "' AND `PASSWORD`='" . $password . "'";
            //return $query;
            return $this->obtenerresultados($query);
        }
    }