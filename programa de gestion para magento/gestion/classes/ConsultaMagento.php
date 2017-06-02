<?php
class ConsultaMagento{
	
	private $server="SERVIDOR";
	private $dataBase="EXPORTADOR_ELITESTORE";
	private $uid="ICGAdmin";
	private $pwd="masterkey";
	
	public function conectar(){
		$server= $this->server;
		$dataBase= $this->dataBase;
		$uid=$this->uid;
		$pwd=$this->pwd;
		$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBase", $uid, $pwd);
		if( $conn === false ){
			echo "No es posible conectarse al servidor.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		return $conn;
	}
	
	public function consultarMagento($conn,$query){
		$stmt = $conn->prepare($query); 
		if( $stmt === false ){
			echo "<br/>Error al ejecutar consulta.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		$stmt->execute();
		$result = $stmt->fetchall(PDO::FETCH_BOTH);
		return $result;
	}
	
	public function consultarStockTemporal(){
		$conn=$this->conectar();
		$sql="IF(EXISTS ( SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'TEMP_STOCKS'))
				BEGIN
					SELECT * FROM TEMP_STOCKS;
				END
			ELSE
				BEGIN
					CREATE TABLE TEMP_STOCKS
						(SKU varchar(50),
						STATUS varchar(50),
						QTY varchar(50));
				END";
		$result=$this->consultarMagento($conn,$sql);
		return $result;
	}
	
	public function insertarTempStock($stocksManager){
			$conn=$this->conectar();
			$sql="INSERT INTO TEMP_STOCKS (SKU,STATUS,QTY) ".$stocksManager; 
			$stmt = $conn->prepare($sql); 
			$stmt->execute();
		
	}
}

?>