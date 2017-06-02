<?php
class ConsultaManager{
	
	private $server="SERVIDOR";
	private $dataBase="G001";
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
	
	public function consultarManager($conn,$query){
		$stmt = $conn->prepare($query); 
		if( $stmt === false ){
			echo "<br/>Error al ejecutar consulta 1.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		$stmt->execute();
		$result = $stmt->fetchall(PDO::FETCH_BOTH);
		return $result;
	}
	
	public function insertar($conn,$query){
		$stmt = $conn->prepare($query); 
		if( $stmt === false ){
			echo "<br/>Error al ejecutar consulta 1.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		try{
			$stmt->execute();
		}catch(Exception $e){
			echo "<br/>Error al insertar los datos</br>";
			echo $e;
			die( print_r( sqlsrv_errors(), true));
		}
	}
	
	public function crearTablaTemporal(){
		$conn=$this->conectar();
		$sql="INSERT INTO SERVIDOR.EXPORTADOR_ELITESTORE.dbo.TEMP_STOCKS SELECT T9.REFPROVEEDOR + isnull(T8.TALLA,'.') + isnull(T8.COLOR,'.') as SKU, 
				CASE T0.TACON  WHEN 'SI' THEN 'Habilitado' WHEN 'NO' THEN 'Deshabilitado' END AS STATUS, 
				isnull((
					select 
						sum(T20.STOCK) 
					from SERVIDOR.G001.dbo.STOCKS T20 
					WHERE 
						T0.CODARTICULO = T20.CODARTICULO 
						AND T8.TALLA = T20.TALLA  
						AND T8.COLOR = T20.COLOR 
						and len(codalmacen)>=2 AND  
						(CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') 
						and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50
						),0) AS QTY 
				FROM SERVIDOR.G001.dbo.ARTICULOS AS T0 
					LEFT OUTER JOIN SERVIDOR.G001.dbo.ARTICULOSLIN T8 
						ON T0.CODARTICULO = T8.CODARTICULO 
					LEFT OUTER JOIN SERVIDOR.G001.dbo.REFERENCIASPROV T9 
						ON T0.CODARTICULO = T9.CODARTICULO 
				where T0.DESCATALOGADO='F' 
					and T0.TACON='SI' 
					and (
						isnull(
							(
								select 
									sum(T20.STOCK) 
								from SERVIDOR.G001.dbo.STOCKS T20 
								WHERE T0.CODARTICULO = T20.CODARTICULO 
									AND T8.TALLA = T20.TALLA  
									AND T8.COLOR = T20.COLOR
									and len(codalmacen)>=2 
									AND  (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') 
									and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0
							)
						)>=-1 
				ORDER BY  T0.CODARTICULO";
		$this->insertar($conn,$sql);
		
	}
	
	
	
	
	public function consultarStock(){
		$conn=$this->conectar();
		$sql="SELECT T9.REFPROVEEDOR + isnull(T8.TALLA,'.') + isnull(T8.COLOR,'.') as SKU, 
				CASE T0.TACON  WHEN 'SI' THEN 'Habilitado' WHEN 'NO' THEN 'Deshabilitado' END AS STATUS, 
				isnull((
					select 
						sum(T20.STOCK) 
					from STOCKS T20 
					WHERE 
						T0.CODARTICULO = T20.CODARTICULO 
						AND T8.TALLA = T20.TALLA  
						AND T8.COLOR = T20.COLOR 
						and len(codalmacen)>=2 AND  
						(CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') 
						and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50
						),0) AS QTY 
				FROM ARTICULOS AS T0 
					LEFT OUTER JOIN ARTICULOSLIN T8 
						ON T0.CODARTICULO = T8.CODARTICULO 
					LEFT OUTER JOIN REFERENCIASPROV T9 
						ON T0.CODARTICULO = T9.CODARTICULO 
				where T0.DESCATALOGADO='F' 
					and T0.TACON='SI' 
					and (
						isnull(
							(
								select 
									sum(T20.STOCK) 
								from STOCKS T20 
								WHERE T0.CODARTICULO = T20.CODARTICULO 
									AND T8.TALLA = T20.TALLA  
									AND T8.COLOR = T20.COLOR
									and len(codalmacen)>=2 
									AND  (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') 
									and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0
							)
						)>=-1 
				ORDER BY  T0.CODARTICULO";
		$result=$this->consultarManager($conn,$sql);
		return $result;
	}
	
}

?>