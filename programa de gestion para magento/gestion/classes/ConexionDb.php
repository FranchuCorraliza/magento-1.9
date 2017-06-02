<?php

	class ConexionDb {
		private $server="192.168.1.201";
		private $uid="ICGAdmin";
		private $pwd="masterkey";
		private $dataBaseManager="G001";
		private $dataBaseTemp="EXPORTADOR_ELITESTORE";
		
		//Realiza la consuta para extraer los stocks que han cambiado desde la �ltima ejecuci�n. Devuelve la consulta de los datos que se deben actualizar
		public function consultarStock(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT * FROM (SELECT DISTINCT 
T9.REFPROVEEDOR + isnull(T8.TALLA,'.') + isnull(T8.COLOR,'.') as sku, 
isnull((
	select sum(T20.STOCK) 
	from STOCKS T20 
	WHERE T0.CODARTICULO = T20.CODARTICULO 
		AND T8.TALLA = T20.TALLA 
		AND T8.COLOR = T20.COLOR 
		and len(codalmacen)>=2 AND
		(CODALMACEN like 'A[0-9]' 
			or CODALMACEN like 'A[0-9][0-9]') 
		and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0) AS qty, 
CASE WHEN((isnull((
	select sum(T20.STOCK) 
	from STOCKS T20 
	WHERE T0.CODARTICULO = T20.CODARTICULO 
		AND T8.TALLA = T20.TALLA 
		AND T8.COLOR = T20.COLOR 
		and len(codalmacen)>=2 AND
		(CODALMACEN like 'A[0-9]' 
			or CODALMACEN like 'A[0-9][0-9]') 
		and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>0) THEN 1 ELSE 0 END as is_in_stock FROM ARTICULOS AS T0 LEFT OUTER JOIN ARTICULOSLIN T8 ON T0.CODARTICULO = T8.CODARTICULO LEFT OUTER JOIN REFERENCIASPROV T9 ON T0.CODARTICULO = T9.CODARTICULO where T0.DESCATALOGADO='F' and T0.TACON='SI' and (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO AND T8.TALLA = T20.TALLA AND T8.COLOR = T20.COLOR	and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1) AS query1 EXCEPT SELECT * FROM (SELECT * FROM EXPORTADOR_ELITESTORE.dbo.TEMP_STOCKS) AS query2";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
		}
		
		public function consultarStockColorYPeso(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT DISTINCT 
						T9.REFPROVEEDOR + isnull(T8.TALLA,'.') + isnull(T8.COLOR,'.') as sku, 
						isnull((
							select sum(T20.STOCK) 
							from STOCKS T20 
							WHERE T0.CODARTICULO = T20.CODARTICULO 
								AND T8.TALLA = T20.TALLA 
								AND T8.COLOR = T20.COLOR 
								and len(codalmacen)>=2 AND
								(CODALMACEN like 'A[0-9]' 
									or CODALMACEN like 'A[0-9][0-9]') 
								and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0) AS qty, 
								CASE WHEN((isnull((
									select sum(T20.STOCK) 
									from STOCKS T20 
									WHERE T0.CODARTICULO = T20.CODARTICULO 
										AND T8.TALLA = T20.TALLA 
										AND T8.COLOR = T20.COLOR 
										and len(codalmacen)>=2 AND
										(CODALMACEN like 'A[0-9]' 
											or CODALMACEN like 'A[0-9][0-9]') 
										and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>0) THEN 1 ELSE 0 END as is_in_stock ,
							T8.COLOR as color,
							T0.DPTO,
							T0.SECCION,
							T0.FAMILIA
					FROM ARTICULOS AS T0 
						LEFT OUTER JOIN ARTICULOSLIN T8 
							ON T0.CODARTICULO = T8.CODARTICULO 
						LEFT OUTER JOIN REFERENCIASPROV T9 
							ON T0.CODARTICULO = T9.CODARTICULO 
					where T0.DESCATALOGADO='F' 
						and T0.TACON='SI' 
						and (
							isnull((select sum(T20.STOCK) 
										from STOCKS T20 
										WHERE T0.CODARTICULO = T20.CODARTICULO 
											AND T8.TALLA = T20.TALLA 
											AND T8.COLOR = T20.COLOR	
											and len(codalmacen)>=2 AND 
											(CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
		}
		public function consultarStockConf(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT * FROM (SELECT DISTINCT 
						T9.REFPROVEEDOR as sku, 
						CASE WHEN ((isnull((
										select sum(T20.STOCK) 
										from STOCKS T20 
										WHERE T0.CODARTICULO = T20.CODARTICULO 
											and len(codalmacen)>=2 AND
											(CODALMACEN like 'A[0-9]' 
												or CODALMACEN like 'A[0-9][0-9]') 
											and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>0) 
							 THEN 1
							 ELSE 0
							 END as is_in_stock FROM ARTICULOS AS T0 LEFT OUTER JOIN REFERENCIASPROV T9 ON T0.CODARTICULO = T9.CODARTICULO where T0.DESCATALOGADO='F' and T0.TACON='SI' and (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1
							 GROUP BY T9.REFPROVEEDOR,T0.CODARTICULO) as query1
					 EXCEPT
					SELECT sku COLLATE Latin1_General_CS_AI, is_in_stock COLLATE Latin1_General_CS_AI FROM (SELECT * FROM EXPORTADOR_ELITESTORE.dbo.TEMP_STOCKS_CONF) as query2";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;
		}
		public function consultarStockYColorConf(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT DISTINCT 
						T9.REFPROVEEDOR as sku,
						T8.COLOR as color_simple ,
						CASE WHEN ((isnull((
										select sum(T20.STOCK) 
										from STOCKS T20 
										WHERE T0.CODARTICULO = T20.CODARTICULO 
											and len(codalmacen)>=2 AND
											(CODALMACEN like 'A[0-9]' 
												or CODALMACEN like 'A[0-9][0-9]') 
											and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>0) 
							 THEN 1
							 ELSE 0
							 END as is_in_stock FROM ARTICULOS AS T0 
							 LEFT OUTER JOIN ARTICULOSLIN AS T8 ON T0.CODARTICULO = T8.CODARTICULO
							 LEFT OUTER JOIN REFERENCIASPROV T9 ON T0.CODARTICULO = T9.CODARTICULO 
							 where T0.DESCATALOGADO='F' and T0.TACON='SI' and (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1
							 GROUP BY T9.REFPROVEEDOR,T0.CODARTICULO,T8.COLOR";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;
		}
		
		public function consultarBase(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT * FROM (SELECT DISTINCT 
						T9.REFPROVEEDOR as sku,
						T0.DESCRIPCION as name,
						T11.CAMPO3 as description,
						T11.CAMPO2 as composicion,
						T11.CAMPO9 as tallaje,
						CASE WHEN (T11.ES_OUTLET='T') THEN 1 ELSE 0 END as outlet,
						CASE WHEN (T11.ES_SALE='T') THEN 1 ELSE 0 END as sale,
						CASE WHEN (T11.ES_UNISEX='T') THEN 1 ELSE 0 END as unisex,
						CASE WHEN (T11.ES_HOME_PIC='T') THEN 1 ELSE 0 END as home_pic,
						CASE WHEN (T11.ES_DESIGNER_PIC='T') THEN 1 ELSE 0 END as designer_pic,
						T11.ES_DESIGNER_LINE as designer_line,
						T11.ES_ESTILISMOS as estilismo,
						T11.ES_TAGS as tags,
						CASE WHEN (T11.ES_RUNAWAY='T') THEN 1 ELSE 0 END as runway,
						T11.ES_CATEGORIAS as category_ids,
						T7.DESCRIPCION as manufacturer,
						T0.CODARTICULO as codarticulo,
						T11.ES_MODELO as modelo,
						CASE WHEN (T11.ES_ORDER_BY_REQUEST='T') THEN 1 ELSE 0 END as order_by_request,
						T11.CAMPO8 as color,
						T0.TEMPORADA as temporada,
						T0.TIPO as tipo
						FROM ARTICULOS AS T0 LEFT OUTER JOIN ARTICULOSLIN T8 ON T0.CODARTICULO = T8.CODARTICULO LEFT OUTER JOIN REFERENCIASPROV T9 ON T0.CODARTICULO = T9.CODARTICULO LEFT OUTER JOIN MARCA T7 ON T0.MARCA = T7.CODMARCA LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T11 ON T0.CODARTICULO = T11.CODARTICULO where T0.DESCATALOGADO='F' and T0.TACON='SI' and (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO	and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1) AS query1 

						EXCEPT 

						SELECT * FROM (SELECT 
						sku collate Latin1_General_CS_AI AS sku,
						nombre collate Latin1_General_CS_AI AS name,
						descripcion AS description,
						composicion collate Latin1_General_CS_AI AS composicion,
						tallaje collate Latin1_General_CS_AI AS tallaje,
						outlet collate Latin1_General_CS_AI AS outlet,
						sale collate Latin1_General_CS_AI AS sale,
						unisex collate Latin1_General_CS_AI AS unisex,
						home_pic collate Latin1_General_CS_AI AS home_pic,
						designer_pic collate Latin1_General_CS_AI AS designer_pic,
						designer_line collate Latin1_General_CS_AI AS designer_line,
						estilismos collate Latin1_General_CS_AI AS estilismo,
						tags collate Latin1_General_CS_AI AS tags,
						runway collate Latin1_General_CS_AI AS runway,
						categorias collate Latin1_General_CS_AI AS category_ids,
						marca collate Latin1_General_CS_AI AS manufacturer,
						codarticulo collate Latin1_General_CS_AI AS codarticulo,
						modelo collate Latin1_General_CS_AI AS modelo,
						order_by_request collate Latin1_General_CS_AI AS order_by_request, 
						color collate Latin1_General_CS_AI as color,
						temporada collate Latin1_General_CS_AI as temporada,
						tipo  collate Latin1_General_CS_AI as tipo
						FROM EXPORTADOR_ELITESTORE.dbo.TEMP_BASE) AS query2;";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
			
		}
		
		
		public function consultarPrecio(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT * FROM (SELECT DISTINCT
						T0.REFPROVEEDOR AS sku,
						ROUND(T2.PNETO/(1+(T4.IVA/100)),2) AS price, 
						T2.DESDE2 AS special_from_date, 
						T2.HASTA2 AS special_to_date,
						CASE 
							WHEN (T1.ES_OUTLET='T')
							THEN 
								ROUND(T3.PNETO2/(1+(T4.IVA/100)),2)
							ELSE
								ROUND(T2.PNETO2/(1+(T4.IVA/100)),2)
							END  AS special_price
						 FROM ARTICULOS T0
						 LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO = T1.CODARTICULO
						 LEFT OUTER JOIN PRECIOSVENTA T2 ON T2.IDTARIFAV = 4 AND T2.CODARTICULO = T0.CODARTICULO  
						 LEFT OUTER JOIN PRECIOSVENTA T3 ON T3.IDTARIFAV = 8 AND T3.CODARTICULO = T0.CODARTICULO 
						 LEFT OUTER JOIN IMPUESTOS AS T4 ON T0.TIPOIMPUESTO = T4.TIPOIVA
						 WHERE 
						 T0.DESCATALOGADO='F' AND T0.TACON='SI' AND
						 (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO
						and len(codalmacen)>=2 AND  (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and
						cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1) AS query1 

						EXCEPT 

						SELECT * FROM (SELECT 
						sku collate Latin1_General_CS_AI AS sku,
						price AS price,
						special_from_date AS special_from_date,
						special_to_date AS special_to_date,
						special_price AS special_price
						FROM EXPORTADOR_ELITESTORE.dbo.TEMP_PRECIOS) AS query2;";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
			
		}
		
		public function consultarIngles(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT * FROM (
						SELECT DISTINCT 
							T0.REFPROVEEDOR as sku, 
							T1.CAMPO1 as name,
							T1.CAMPO6 as description
						FROM ARTICULOS AS T0 
						LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO = T1.CODARTICULO 
						WHERE T0.DESCATALOGADO='F' 
							and T0.TACON='SI' 
							and (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1) as query1
					EXCEPT
					SELECT sku COLLATE Latin1_General_CS_AI, name COLLATE Latin1_General_CS_AI, description COLLATE Latin1_General_CS_AI FROM (SELECT * FROM EXPORTADOR_ELITESTORE.dbo.TEMP_INGLES) as query2;";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
			
		}
		
		
		public function consultarNuevos(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT * FROM (SELECT DISTINCT 
										T0.REFPROVEEDOR + isnull(T1.TALLA,'.') + isnull(T1.COLOR,'.') as sku, 
										T0.CODARTICULO as codarticulo
									FROM ARTICULOS AS T0 LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO = T1.CODARTICULO 
									WHERE T0.DESCATALOGADO='F' and T0.TACON='SI' and (isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO AND T1.TALLA = T20.TALLA AND T1.COLOR = T20.COLOR	and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=-1) AS query1 
					EXCEPT SELECT sku collate Latin1_General_CS_AI, codarticulo collate Latin1_General_CS_AI FROM (SELECT * 
									FROM EXPORTADOR_ELITESTORE.dbo.TEMP_NUEVOS) AS query2";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
			
		}
		
		public function consultarImagenes(){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT sku FROM ARTICULOSCAMPOSLIBRES WHERE ES_ACTUALIZARIMAGENES='T'";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
		}
		
		public function getArticulos($codarticulos){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT DISTINCT
						T0.REFPROVEEDOR + isnull(T1.TALLA,'.') + isnull(T1.COLOR,'.') as sku,
						T0.DESCRIPCION as name,
						T2.CAMPO3 as description,
						T1.TALLA as talla,
						T2.CAMPO2 as composicion,
						T2.CAMPO9 as tallaje,
						T2.ES_MODELO as modelo,
						CASE WHEN (T2.ES_OUTLET='T') THEN 1 ELSE 0 END as outlet,
						CASE WHEN (T2.ES_SALE='T') THEN 1 ELSE 0 END as sale,
						CASE WHEN (T2.ES_UNISEX='T') THEN 1 ELSE 0 END as unisex,
						CASE WHEN (T2.ES_HOME_PIC='T') THEN 1 ELSE 0 END as home_pic,
						CASE WHEN (T2.ES_DESIGNER_PIC='T') THEN 1 ELSE 0 END as designer_pic,
						'' as designer_line,
						'' as estilismo,
						'' as tags,
						CASE WHEN (T2.ES_RUNAWAY='T') THEN 1 ELSE 0 END as runway,
						T2.ES_CATEGORIAS as category_ids,
						T3.DESCRIPCION as manufacturer,
						CASE WHEN (T2.ES_ORDER_BY_REQUEST='T') THEN 1 ELSE 0 END as order_by_request,
						'' as color,
						T0.TEMPORADA as temporada,
						T0.TIPO as tipo,
						isnull((
							select sum(T20.STOCK) 
							from STOCKS T20 
							WHERE T0.CODARTICULO = T20.CODARTICULO 
								AND T1.TALLA = T20.TALLA 
								AND T1.COLOR = T20.COLOR 
								and len(codalmacen)>=2 AND
								(CODALMACEN like 'A[0-9]' 
									or CODALMACEN like 'A[0-9][0-9]') 
								and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0) AS qty,
						ROUND(T4.PNETO/(1+(T6.IVA/100)),2) AS price,
						CASE 
							WHEN (T2.ES_OUTLET='T') 
							THEN 
								CASE WHEN (T2.VISIBLE_WEB='F')
									THEN
										T4.DESDE2
									ELSE
										T5.DESDE2
								END
							ELSE
								T4.DESDE2
							END 
							AS special_from_date, 
						CASE 
							WHEN (T2.ES_OUTLET='T')
							THEN 
								CASE WHEN (T2.VISIBLE_WEB='F')
									THEN
										T4.HASTA2
									ELSE
										T5.HASTA2
								END
							ELSE
								T4.HASTA2
							END
							AS special_to_date,
						CASE 
							WHEN (T2.ES_OUTLET='T')
							THEN 
								ROUND(T5.PNETO2/(1+(T6.IVA/100)),2)
							ELSE
								ROUND(T4.PNETO2/(1+(T6.IVA/100)),2)
						END  AS special_price,
						T2.ES_IMAGE1 AS image,
						T2.ES_IMAGE1 AS small_image,
						T2.ES_IMAGE1 AS thumbnail,
						ISNULL(T2.ES_IMAGE1+';','')+ISNULL(T2.ES_IMAGE2+'::back;','')+ISNULL(T2.ES_IMAGE3+';','')+ISNULL(T2.ES_IMAGE4+';','')+ISNULL(T2.ES_IMAGE5+';','')+ISNULL(T2.ES_IMAGE6+';','')+ISNULL(T2.ES_IMAGE7+';','') AS media_gallery,
						T2.ES_VIDEO AS video_url,
						T0.REFPROVEEDOR AS sku_configurable,
						T2.CAMPO1 AS name_en,
						T2.CAMPO6 AS description_en,
						T0.CODARTICULO AS codarticulo,
						T2.FECHA_SUBIDA AS fecha_subida,
						T1.CODBARRAS AS codbarras,
						T1.COLOR AS color_simple,
						T0.DPTO AS dpto,
						T0.SECCION as seccion,
						T0.FAMILIA AS familia
				FROM ARTICULOS T0 
					LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO
					LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T2 ON T0.CODARTICULO=T2.CODARTICULO
					LEFT OUTER JOIN MARCA T3 ON T0.MARCA = T3.CODMARCA
					LEFT OUTER JOIN PRECIOSVENTA T4 ON T4.IDTARIFAV = 4 AND T4.CODARTICULO = T0.CODARTICULO  
					LEFT OUTER JOIN PRECIOSVENTA T5 ON T5.IDTARIFAV = 8 AND T5.CODARTICULO = T0.CODARTICULO 
					LEFT OUTER JOIN IMPUESTOS AS T6 ON T0.TIPOIMPUESTO = T6.TIPOIVA
					WHERE T0.CODARTICULO IN ($codarticulos) ORDER BY codarticulo";
			/*
			$query="SELECT DISTINCT
						T0.REFPROVEEDOR + isnull(T1.TALLA,'.') + isnull(T1.COLOR,'.') as sku,
						T0.DESCRIPCION as name,
						T2.CAMPO3 as description,
						T1.TALLA as talla,
						T2.CAMPO2 as composicion,
						T2.CAMPO9 as tallaje,
						T2.ES_MODELO as modelo,
					  CASE WHEN (T2.ES_OUTLET='T') THEN 1 ELSE 0 END as outlet,
						CASE WHEN (T2.ES_SALE='T') THEN 1 ELSE 0 END as sale,
						CASE WHEN (T2.ES_UNISEX='T') THEN 1 ELSE 0 END as unisex,
						CASE WHEN (T2.ES_HOME_PIC='T') THEN 1 ELSE 0 END as home_pic,
						CASE WHEN (T2.ES_DESIGNER_PIC='T') THEN 1 ELSE 0 END as designer_pic,
						T2.ES_DESIGNER_LINE as designer_line,
						T2.ES_ESTILISMOS as estilismo,
						T2.ES_TAGS as tags,
						CASE WHEN (T2.ES_RUNAWAY='T') THEN 1 ELSE 0 END as runway,
						T2.ES_CATEGORIAS as category_ids,
						T3.DESCRIPCION as manufacturer,
						CASE WHEN (T2.ES_ORDER_BY_REQUEST='T') THEN 1 ELSE 0 END as order_by_request,
						T2.CAMPO8 as color,
						T0.TEMPORADA as temporada,
						T0.TIPO as tipo,
						isnull((
							select sum(T20.STOCK) 
							from STOCKS T20 
							WHERE T0.CODARTICULO = T20.CODARTICULO 
								AND T1.TALLA = T20.TALLA 
								AND T1.COLOR = T20.COLOR 
								and len(codalmacen)>=2 AND
								(CODALMACEN like 'A[0-9]' 
									or CODALMACEN like 'A[0-9][0-9]') 
								and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0) AS qty,
						ROUND(T4.PNETO/(1+(T6.IVA/100)),2) AS price,
						T4.DESDE2 AS special_from_date, 
						T4.HASTA2 AS special_to_date,
						CASE 
							WHEN (T2.ES_OUTLET='T')
							THEN 
								ROUND(T5.PNETO2/(1+(T6.IVA/100)),2)
							ELSE
								ROUND(T4.PNETO2/(1+(T6.IVA/100)),2)
						END  AS special_price,
						T2.ES_IMAGE1 AS image,
						T2.ES_IMAGE1 AS small_image,
						T2.ES_IMAGE1 AS thumbnail,
						ISNULL(T2.ES_IMAGE1+';','')+ISNULL(T2.ES_IMAGE2+'::back;','')+ISNULL(T2.ES_IMAGE3+';','')+ISNULL(T2.ES_IMAGE4+';','')+ISNULL(T2.ES_IMAGE5+';','')+ISNULL(T2.ES_IMAGE6+';','')+ISNULL(T2.ES_IMAGE7+';','') AS media_gallery,
						T2.ES_VIDEO AS video_url,
						T0.REFPROVEEDOR AS sku_configurable,
						T2.CAMPO1 AS name_en,
						T2.CAMPO6 AS description_en,
						T0.CODARTICULO AS codarticulo
				FROM ARTICULOS T0 
					LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO
					LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T2 ON T0.CODARTICULO=T2.CODARTICULO
					LEFT OUTER JOIN MARCA T3 ON T0.MARCA = T3.CODMARCA
					LEFT OUTER JOIN PRECIOSVENTA T4 ON T4.IDTARIFAV = 4 AND T4.CODARTICULO = T0.CODARTICULO  
					LEFT OUTER JOIN PRECIOSVENTA T5 ON T5.IDTARIFAV = 8 AND T5.CODARTICULO = T0.CODARTICULO 
					LEFT OUTER JOIN IMPUESTOS AS T6 ON T0.TIPOIMPUESTO = T6.TIPOIVA
					WHERE T0.CODARTICULO IN ($codarticulos) ORDER BY codarticulo";
			*/
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
		}
		public function getDescripcionesIngles($codarticulos){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT T0.REFPROVEEDOR AS sku, T1.CAMPO1 AS name, T1.CAMPO6 AS description FROM ARTICULOS T0 LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO WHERE T0.CODARTICULO IN ($codarticulos)";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
		}
		
		public function getImagenes($sku){
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$query="SELECT TO.REFPROVEEDOR, 
						T1.ES_IMAGE1 AS image,
						T1.ES_IMAGE1 AS small_image,
						T1.ES_IMAGE1 AS thumbnail,
						ISNULL(T1.ES_IMAGE1+';','')+ISNULL(T1.ES_IMAGE2+'::back;','')+ISNULL(T1.ES_IMAGE3+';','')+ISNULL(T1.ES_IMAGE4+';','')+ISNULL(T1.ES_IMAGE5+';','')+ISNULL(T1.ES_IMAGE6+';','')+ISNULL(T1.ES_IMAGE7+';','') AS media_gallery,
						T1.ES_VIDEO AS video_url
					FROM ARTICULOS T0 LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
					WHERE TO.REFPROVEEDOR='$sku'";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;	
		}
		//Inserta la consulta de stocks realizada para actualizar la actual.
		public function actualizarTablaStocksTemporal($stocksActualizar){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			foreach ($stocksActualizar as $itemActualizar){
				$query="IF EXISTS(SELECT sku FROM TEMP_STOCKS WHERE sku='".$itemActualizar['sku']."')
					BEGIN
						UPDATE  TEMP_STOCKS SET qty='".$itemActualizar['qty']."',is_in_stock='".$itemActualizar['is_in_stock']."' WHERE sku='".$itemActualizar['sku']."';
					END
				ELSE
					BEGIN
						INSERT INTO TEMP_STOCKS (sku,qty,is_in_stock) VALUES ('".$itemActualizar['sku']."','".$itemActualizar['qty']."','".$itemActualizar['is_in_stock']."');
					END";
				$stmt = $conn->prepare($query); 
				if( $stmt === false ){
					throw new Exception("Error al ejecutar consulta.");
					die();
				}
				$stmt->execute();
				//$result = $stmt->fetchall(PDO::FETCH_BOTH);
			}
		}
		
		public function actualizarTablaStocksConfTemporal($stocksActualizar){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			foreach ($stocksActualizar as $itemActualizar){
				$query="IF EXISTS(SELECT sku FROM TEMP_STOCKS_CONF WHERE sku='".$itemActualizar['sku']."')
					BEGIN
						UPDATE  TEMP_STOCKS_CONF SET is_in_stock='".$itemActualizar['is_in_stock']."' WHERE sku='".$itemActualizar['sku']."';
					END
				ELSE
					BEGIN
						INSERT INTO TEMP_STOCKS_CONF (sku,is_in_stock) VALUES ('".$itemActualizar['sku']."','".$itemActualizar['is_in_stock']."');
					END";
				$stmt = $conn->prepare($query); 
				if( $stmt === false ){
					throw new Exception("Error al ejecutar consulta.");
					die();
				}
				$stmt->execute();
				//$result = $stmt->fetchall(PDO::FETCH_BOTH);
			}
		}
		
		public function actualizarTablaBaseTemporal($stocksActualizar){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			foreach ($stocksActualizar as $itemActualizar){
				$query="IF EXISTS(SELECT sku FROM TEMP_BASE WHERE sku='".$itemActualizar['sku']."')
					BEGIN
						UPDATE  TEMP_BASE 
							SET nombre='".$itemActualizar['name']."',
								descripcion='".$itemActualizar['description']."',
								composicion='".$itemActualizar['composicion']."',
								tallaje='".$itemActualizar['tallaje']."',
								outlet='".$itemActualizar['outlet']."',
								sale='".$itemActualizar['sale']."',
								unisex='".$itemActualizar['unisex']."',
								home_pic='".$itemActualizar['home_pic']."',
								designer_pic='".$itemActualizar['designer_pic']."',
								designer_line='".$itemActualizar['designer_line']."',
								estilismos='".$itemActualizar['estilismo']."',
								tags='".$itemActualizar['tags']."',
								runway='".$itemActualizar['runway']."',
								categorias='".$itemActualizar['category_ids']."',
								marca='".$itemActualizar['manufacturer']."',
								codarticulo='".$itemActualizar['codarticulo']."',
								modelo='".$itemActualizar['modelo']."',
								order_by_request='".$itemActualizar['order_by_request']."',
								color='".$itemActualizar['color']."',
								temporada='".$itemActualizar['temporada']."',
								tipo='".$itemActualizar['tipo']."'
								WHERE sku='".$itemActualizar['sku']."';
					END
				ELSE
					BEGIN
						INSERT INTO TEMP_BASE (sku,
													nombre,
													descripcion,
													composicion,
													tallaje,
													outlet,
													sale,
													unisex,
													home_pic,
													designer_pic,
													designer_line,
													estilismos,
													tags,
													runway,
													categorias,
													marca,
													codarticulo,
													modelo,
													order_by_request,
													color,
													temporada,
													tipo) 
								VALUES ('".$itemActualizar['sku']."',
										'".$itemActualizar['name']."',
										'".$itemActualizar['description']."',
										'".$itemActualizar['composicion']."',
										'".$itemActualizar['tallaje']."',
										'".$itemActualizar['outlet']."',
										'".$itemActualizar['sale']."',
										'".$itemActualizar['unisex']."',
										'".$itemActualizar['home_pic']."',
										'".$itemActualizar['designer_pic']."',
										'".$itemActualizar['designer_line']."',
										'".$itemActualizar['estilismo']."',
										'".$itemActualizar['tags']."',
										'".$itemActualizar['runway']."',
										'".$itemActualizar['category_ids']."',
										'".$itemActualizar['manufacturer']."',
										'".$itemActualizar['codarticulo']."',
										'".$itemActualizar['modelo']."',
										'".$itemActualizar['order_by_request']."',
										'".$itemActualizar['color']."',
										'".$itemActualizar['temporada']."',
										'".$itemActualizar['tipo']."');
					END";
				$stmt = $conn->prepare($query); 
				if( $stmt === false ){
					throw new Exception("Error al ejecutar consulta.");
					die();
				}
				$stmt->execute();
				//$result = $stmt->fetchall(PDO::FETCH_BOTH);
			}
		}
		
		public function actualizarTablaPrecioTemporal($stocksActualizar){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			foreach ($stocksActualizar as $itemActualizar){
				$query="IF EXISTS(SELECT sku FROM TEMP_BASE WHERE sku='".$itemActualizar['sku']."')
					BEGIN
						UPDATE  TEMP_BASE 
							SET price='".$itemActualizar['price']."',
								special_to_date='".$itemActualizar['special_to_date']."',
								special_from_date='".$itemActualizar['special_from_date']."',
								special_price='".$itemActualizar['special_price']."'
								WHERE sku='".$itemActualizar['sku']."';
					END
				ELSE
					BEGIN
						INSERT INTO TEMP_BASE (sku,
													price,
													special_to_date,
													special_from_date,
													special_price
													) 
								VALUES ('".$itemActualizar['sku']."',
										'".$itemActualizar['price']."',
										'".$itemActualizar['special_to_date']."',
										'".$itemActualizar['special_from_date']."',
										'".$itemActualizar['special_price']."',
										);
					END";
				$stmt = $conn->prepare($query); 
				if( $stmt === false ){
					throw new Exception("Error al ejecutar consulta.");
					die();
				}
				$stmt->execute();
				//$result = $stmt->fetchall(PDO::FETCH_BOTH);
			}
		}
		
		
		public function actualizarTablaInglesTemporal($stocksActualizar){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			foreach ($stocksActualizar as $itemActualizar){
				$query="IF EXISTS(SELECT sku FROM TEMP_INGLES WHERE sku='".$itemActualizar['sku']."')
					BEGIN
						UPDATE  TEMP_INGLES 
							SET name='".$itemActualizar['name']."',
								description='".$itemActualizar['description']."'
								WHERE sku='".$itemActualizar['sku']."';
					END
				ELSE
					BEGIN
						INSERT INTO TEMP_INGLES (sku,
													name,
													description
													) 
								VALUES ('".$itemActualizar['sku']."',
										'".$itemActualizar['name']."',
										'".$itemActualizar['description']."'
										);
					END";
				$stmt = $conn->prepare($query); 
				if( $stmt === false ){
					throw new Exception("Error al ejecutar consulta.");
					die();
				}
				$stmt->execute();
				//$result = $stmt->fetchall(PDO::FETCH_BOTH);
			}
		}
		
		public function actualizarTablaNuevosTemporal($stocksActualizar){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			foreach ($stocksActualizar as $itemActualizar){
				$query="INSERT INTO TEMP_NUEVOS (sku,
													codarticulo
													) 
								VALUES ('".$itemActualizar['sku']."',
										'".$itemActualizar['codarticulo']."'
										);";
				$stmt = $conn->prepare($query); 
				if( $stmt === false ){
					throw new Exception("Error al ejecutar consulta.");
					die();
				}
				$stmt->execute();
				//$result = $stmt->fetchall(PDO::FETCH_BOTH);
			}
		}
		
		
		public function getTablaCategorias(){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			$query="SELECT * FROM CATEGORIAS";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;
		}
		public function updateTablaCategorias($data){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			$query="IF EXISTS(SELECT * FROM CATEGORIAS WHERE ID=$data[6])
					UPDATE CATEGORIAS SET DPTO=$data[6],SEC=$data[1],FAM=$data[2],SALE=$data[3],OUTLET=$data[4],CATEGORY=$data[5] WHERE ID=$data[6]
				ELSE
					INSERT INTO CATEGORIAS (DPTO, SEC, FAM, SALE, OUTLET, CATEGORY, ID) VALUES ($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6])";
					echo $query;
			$stmt = $conn->prepare($query); 
			$stmt->execute();
		}
		
		public function getTablaObr(){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			$query="SELECT * FROM ORDERBYREQUEST";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;
		}
		public function updateTablaObr($data){
			$server= $this->server;
			$dataBaseTemp= $this->dataBaseTemp;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
			if( $conn === false ){
				throw new Exception( "No es posible conectarse al servidor.");
				die();
			}
			$query="IF EXISTS(SELECT * FROM ORDERBYREQUEST WHERE ID=$data[2])
					UPDATE ORDERBYREQUEST SET MANUFACTURER='$data[0]',OBR=$data[1] WHERE ID=$data[2]
				ELSE
					INSERT INTO ORDERBYREQUEST (MANUFACTURER,OBR,ID) VALUES ('$data[0]',$data[1],$data[2])";
					echo $query;
			$stmt = $conn->prepare($query); 
			$stmt->execute();
		}
		public function getSkus($codarticulos){
			$skus=array();
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			
			foreach ($codarticulos as $codarticulo){
				$query="SELECT REFPROVEEDOR FROM REFERENCIASPROV WHERE CODARTICULO='$codarticulo'";
				$stmt = $conn->prepare($query); 
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_BOTH);
				if ($result === false){
					throw new Exception("La consulta devuelve False para el codigo: $codarticulo");
				}
				$skus[]=$result[0];
			}
			return $skus;
			
		}
		
		public function getDatos($codarticulos){
			$skus=array();
			$server= $this->server;
			$dataBaseManager= $this->dataBaseManager;
			$uid=$this->uid;
			$pwd=$this->pwd;
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$codigos=implode(',',$codarticulos);
			$query="SELECT	T0.CODARTICULO AS codarticulo,
							T1.REFPROVEEDOR AS sku,
							T0.DESCRIPCION AS nombre_es,
							T2.CAMPO3 AS descripcion_es,
							T2.CAMPO1 AS nombre_en,
							T2.CAMPO6 AS descripcion_en,
							T3.DESCRIPCION AS marca,
							T2.CAMPO9 AS tallaje,
							T2.ES_CATEGORIAS AS categorias,
							T2.ES_COLOR AS color,
							T2.CAMPO2 AS composicion,
							T2.ES_TAGS AS etiquetas,
							T0.TIPO AS temporada,
							T2.ES_ESTILISMOS AS estilismos 
					FROM ARTICULOS T0 
						INNER JOIN REFERENCIASPROV T1 ON T0.CODARTICULO=T1.CODARTICULO 
						INNER JOIN ARTICULOSCAMPOSLIBRES T2 ON T0.CODARTICULO=T2.CODARTICULO
						INNER JOIN MARCA T3 ON T0.MARCA=T3.CODMARCA
						WHERE T0.CODARTICULO IN ($codigos)";
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			return $result;
		}
         public function getCountResults($busqueda, $valor){
            $busqueda=explode(",",$busqueda);
            $valor=explode(",",$valor);
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
            $query="SELECT DISTINCT
                            T0.CODARTICULO AS codarticulo,
                            T0.TACON AS visibleweb,
                            T0.DESCRIPCION AS nombre_es,
                            T0.TIPO AS temporada,
                            T1.REFPROVEEDOR AS sku,
                            T2.CAMPO3 AS descripcion_es,
                            T2.CAMPO1 AS nombre_en,
                            T2.CAMPO6 AS descripcion_en,
                            T2.CAMPO9 AS tallaje,
                            T2.CAMPO4 AS CAMPO4,
                            T2.CAMPO5 AS CAMPO5,
                            T2.ES_CATEGORIAS AS categorias,
                            T2.ES_COLOR AS color,
                            T2.CAMPO2 AS composicion,
                            T2.ES_IMAGE1 AS imagen1,
                            T2.ES_IMAGE2 AS imagen2,
                            T2.ES_IMAGE3 AS imagen3,
                            T2.ES_IMAGE4 AS imagen4,
                            T2.ES_IMAGE5 AS imagen5,
                            T2.ES_IMAGE6 AS imagen6,
                            T2.ES_IMAGE7 AS imagen7,
                            T2.ES_VIDEO AS video,
                            T2.ES_OUTLET AS outlet,
                            T2.ES_SALE AS sale,
                            T2.ES_UNISEX AS unisex,
                            T2.ES_HOME_PIC AS home_pic,
                            T2.ES_DESIGNER_PIC AS designer_pic,
                            T2.ES_DESIGNER_LINE AS designer_line,
                            T2.ES_ESTILISMOS AS estilismos,
                            T2.ES_RUNAWAY AS runway,
                            T2.ES_MODELO AS modelo,
                            T2.ES_ORDER_BY_REQUEST AS order_by_request,
                            T2.ES_TAGS AS tags,
                            T3.DESCRIPCION AS marca,
                            ROW_NUMBER() OVER (ORDER BY T0.CODARTICULO) AS RowNum
                            
                    FROM ARTICULOS T0 
                        INNER JOIN REFERENCIASPROV T1 ON T0.CODARTICULO=T1.CODARTICULO 
                        INNER JOIN ARTICULOSCAMPOSLIBRES T2 ON T0.CODARTICULO=T2.CODARTICULO
                        ";  
                        if(in_array('albaran', $busqueda))
                            {
                                $query = $query . " INNER JOIN ALBCOMPRALIN T4 ON T0.CODARTICULO=T4.CODARTICULO";
                            }
                            $query = $query . " INNER JOIN MARCA T3 ON T0.MARCA=T3.CODMARCA WHERE T0.DESCATALOGADO='F' ";
                for ($i=0; $i<count($busqueda);$i++) {

                    if($busqueda[$i] == 'temporada')
                    {
                        $query = $query . " AND T0.TEMPORADA = '". strtoupper($valor[$i]) ."'";
                    }
                    if($busqueda[$i] == 'referencia')
                    {
                        $query = $query . " AND T0.CODARTICULO = '" . $valor[$i] . "'";
                    }
                    if($busqueda[$i] == 'marca')
                    {
                        $query = $query . " AND T3.DESCRIPCION LIKE '%" . strtoupper($valor[$i]) . "%'";
                    }
                    if($busqueda[$i] == 'nombre')
                    {
                        $query = $query . " AND T0.DESCRIPCION LIKE '%" . strtoupper($valor[$i]) . "%'";
                    }
                    if($busqueda == 'referenciaimagenes')
                    {
                        $query = $query . " AND T0.CODARTICULO IN ('" . $valor . "')";
                    }  
                    if($busqueda[$i] == 'tipo')
                    {
                        $query = $query . " AND T0.TIPO = '" . $valor[$i] . "'";
                    } 
                    if($busqueda[$i] == 'albaran')
                    {
                        $albaran=explode(' ', $valor[$i]);
                        if(count($albaran)==2){
                           $query = $query ." AND T4.NUMSERIE='".$albaran[0]."' AND T4.NUMALBARAN=".$albaran[1];
                        }
                    } 
                    if($busqueda[$i] == 'sku')
                    {
                        $query = $query . " AND T0.REFERENCIASPROV = '" . $valor[$i] . "'";
                    }
                }
                $query = $query . " GROUP BY T0.CODARTICULO, T0.TACON, T0.DESCRIPCION, T0.TEMPORADA, T0.TIPO, T1.REFPROVEEDOR, T2.CAMPO3, T2.CAMPO1, T2.CAMPO6, T2.CAMPO9, T2.CAMPO4, T2.CAMPO5, T2.ES_CATEGORIAS, T2.CAMPO7, T2.CAMPO2, T2.ES_IMAGE1, T2.ES_IMAGE2, T2.ES_IMAGE3, T2.ES_IMAGE4, T2.ES_IMAGE5, T2.ES_IMAGE6, T2.ES_IMAGE7, T2.ES_VIDEO, T2.ES_OUTLET, T2.ES_SALE, T2.ES_UNISEX, T2.ES_HOME_PIC, T2.ES_DESIGNER_PIC, T2.ES_DESIGNER_LINE, T2.ES_ESTILISMOS, T2.ES_RUNAWAY, T2.ES_MODELO, T2.ES_ORDER_BY_REQUEST, T2.ES_TAGS,T3.DESCRIPCION";
            $stmt = $conn->prepare($query); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if ($result === false){
                throw new Exception("La consulta devuelve False");
            }
            return count($result);
        }      
         public function getResults($busqueda, $valor, $inicio,$fin){
            $busqueda=explode(";",$busqueda);
            $valor=explode(";",$valor);
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
            $query="
            SELECT DISTINCT *
            FROM (
            SELECT DISTINCT
                            T0.CODARTICULO AS codarticulo,
                            T0.TACON AS visibleweb,
                            T0.DESCRIPCION AS nombre_es,
                            T0.TEMPORADA AS temporada,
                            T0.TIPO AS tipo,
                            T1.REFPROVEEDOR AS sku,
                            T2.CAMPO3 AS descripcion_es,
                            T2.CAMPO1 AS nombre_en,
                            T2.CAMPO6 AS descripcion_en,
                            T2.CAMPO9 AS tallaje,
                            T2.CAMPO4 AS CAMPO4,
                            T2.CAMPO5 AS CAMPO5,
                            T2.ES_CATEGORIAS AS categorias,
                            T2.ES_COLOR AS color,
                            T2.CAMPO2 AS composicion,
                            T2.ES_IMAGE1 AS imagen1,
                            T2.ES_IMAGE2 AS imagen2,
                            T2.ES_IMAGE3 AS imagen3,
                            T2.ES_IMAGE4 AS imagen4,
                            T2.ES_IMAGE5 AS imagen5,
                            T2.ES_IMAGE6 AS imagen6,
                            T2.ES_IMAGE7 AS imagen7,
                            T2.ES_VIDEO AS video,
                            T2.ES_OUTLET AS outlet,
                            T2.ES_SALE AS sale,
                            T2.ES_UNISEX AS unisex,
                            T2.ES_HOME_PIC AS home_pic,
                            T2.ES_DESIGNER_PIC AS designer_pic,
                            T2.ES_DESIGNER_LINE AS designer_line,
                            T2.ES_ESTILISMOS AS estilismos,
                            T2.ES_RUNAWAY AS runway,
                            T2.ES_MODELO AS modelo,
                            T2.ES_ORDER_BY_REQUEST AS order_by_request,
                            T2.ES_TAGS AS tags,
                            T3.DESCRIPCION AS marca,
                            ROW_NUMBER() OVER (ORDER BY T0.CODARTICULO) AS RowNum
                            
                    FROM ARTICULOS T0 
                        INNER JOIN REFERENCIASPROV T1 ON T0.CODARTICULO=T1.CODARTICULO 
                        INNER JOIN ARTICULOSCAMPOSLIBRES T2 ON T0.CODARTICULO=T2.CODARTICULO
                        ";  
                        if(in_array('albaran', $busqueda))
                            {
                                $query = $query . " INNER JOIN ALBCOMPRALIN T4 ON T0.CODARTICULO=T4.CODARTICULO";
                            }
                            $query = $query . " INNER JOIN MARCA T3 ON T0.MARCA=T3.CODMARCA WHERE T0.DESCATALOGADO='F' ";
                for ($i=0; $i<count($busqueda);$i++) {
                    if($busqueda[$i]!="")
                    {

                        if($busqueda[$i] == 'temporada')
                        {
                            $query = $query . " AND T0.TEMPORADA = '". strtoupper($valor[$i]) ."'";
                        }
                        if($busqueda[$i] == 'referencia')
                        {
                            $query = $query . " AND T0.CODARTICULO = '" . $valor[$i] . "'";
                        }
                        if($busqueda[$i] == 'marca')
                        {
                            $query = $query . " AND T3.DESCRIPCION LIKE '%" . strtoupper($valor[$i]) . "%'";
                        }
                        if($busqueda[$i] == 'nombre')
                        {
                            $query = $query . " AND T0.DESCRIPCION LIKE '%" . strtoupper($valor[$i]) . "%'";
                        }
                        if($busqueda[$i] == 'imagenes')
                        {
                            $query = $query . "AND T0.CODARTICULO IN ('" . $valor[$i] . "')";
                        }  
                        if($busqueda[$i] == 'tipo')
                        {
                            $query = $query . " AND T0.TIPO = '" . $valor[$i] . "'";
                        } 
                        if($busqueda[$i] == 'albaran')
                        {
                            $albaran=explode(' ', $valor[$i]);
                            if(count($albaran)==2){
                               $query = $query ." AND T4.NUMSERIE='".$albaran[0]."' AND T4.NUMALBARAN=".$albaran[1];
                            }
                        } 
                        if($busqueda[$i] == 'sku')
                        {
                            $query = $query . " AND T1.REFPROVEEDOR = '" . strtoupper($valor[$i]) . "'";
                        } 
                        if($busqueda[$i] == 'asignarColor')
                        {
                            $query = $query . " AND T2.ES_COLOR IS NULL AND T0.TACON = 'SI'";
                        } 
                    }
                }
                $query = $query . " GROUP BY
T0.CODARTICULO, T0.TACON, T0.DESCRIPCION, T0.TEMPORADA, T0.TIPO, T1.REFPROVEEDOR, T2.CAMPO3, T2.CAMPO1, T2.CAMPO6, T2.CAMPO9, T2.CAMPO4, T2.CAMPO5, T2.ES_CATEGORIAS, T2.CAMPO7, T2.CAMPO2, T2.ES_IMAGE1, T2.ES_IMAGE2, T2.ES_IMAGE3, T2.ES_IMAGE4, T2.ES_IMAGE5, T2.ES_IMAGE6, T2.ES_IMAGE7, T2.ES_VIDEO, T2.ES_OUTLET, T2.ES_SALE, T2.ES_UNISEX, T2.ES_HOME_PIC, T2.ES_DESIGNER_PIC, T2.ES_DESIGNER_LINE, T2.ES_ESTILISMOS, T2.ES_RUNAWAY, T2.ES_MODELO, T2.ES_ORDER_BY_REQUEST, T2.ES_COLOR, T2.ES_TAGS,T3.DESCRIPCION )AS ROWCOUN WHERE ROWCOUN.RowNum BETWEEN ";
$query = $query . $inicio;
$query = $query . " AND "; 
$query = $query . $fin;

            $stmt = $conn->prepare($query); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if ($result === false){
                throw new Exception("La consulta devuelve False");
            }
            return json_encode ($result);
        }

        public function setParametros($idDelArticulo,$nombre_es,$nombre_en,$descripcion_es,$descripcion_en,$marca,$temporada,$tallaje,$modelo,$video_url,$runway,$estilismo,$outlet,$sale,$unisex,$orderbyrequest,$sku,$composicion,$categorias,$color,$video,$homepic,$designerpic,$designerLine,$tags,$visibleweb,$tipo)
        {
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
            $query="UPDATE  ARTICULOS SET TACON = '" . $visibleweb . "'  WHERE   CODARTICULO = $idDelArticulo";

            $stmt = $conn->prepare($query); 
            $stmt->execute();

            $query="UPDATE  ARTICULOSCAMPOSLIBRES
                    SET     CAMPO3 = '" . $descripcion_es . "',
                            CAMPO1 = '" . $nombre_en . "',
                            CAMPO6 = '" . $descripcion_en . "',
                            CAMPO9 = '" . $tallaje . "',
                            ES_VIDEO = '" . $video . "',
                            ES_OUTLET = '" . $outlet . "',
                            ES_SALE = '" . $sale . "',
                            ES_UNISEX = '" . $unisex . "',
                            ES_HOME_PIC = '" . $homepic . "',
                            ES_DESIGNER_PIC = '" . $designerpic . "',
                            ES_DESIGNER_LINE = '" . $designerLine . "',
                            ES_ESTILISMOS = '" . $estilismo . "',
                            ES_TAGS = '" . $tags . "',
                            ES_RUNAWAY= '" . $runway . "',
                            ES_CATEGORIAS = '" . $categorias . "',
                            ES_MODELO = '" . $modelo . "',
                            CAMPO7 = '". $color . "',
                            CAMPO2 = '". $composicion . "',
                            ES_ORDER_BY_REQUEST = '" . $orderbyrequest . "'
                    WHERE   CODARTICULO = $idDelArticulo";
            $stmt = $conn->prepare($query); 
            $stmt->execute();
            return $query;

        }
         public function setRutaImagen($idDelArticulo,$campo,$valor)
        {
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="UPDATE ARTICULOSCAMPOSLIBRES SET " . $campo . "='" . $valor . "' 
					FROM ARTICULOSCAMPOSLIBRES T0 
						LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO 
					WHERE T1.CODBARRAS='" . $idDelArticulo."'";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
            return "Imagen guardada correctamente";

        }
		public function setPasarelaImagen($idDelArticulo,$valor)
        {
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="UPDATE ARTICULOSCAMPOSLIBRES SET
					ES_IMAGE7=ES_IMAGE6,
					ES_IMAGE6=ES_IMAGE5,
					ES_IMAGE5=ES_IMAGE4,
					ES_IMAGE4=ES_IMAGE3,
					ES_IMAGE3=ES_IMAGE2,
					ES_IMAGE2='" . $valor . "'
					FROM ARTICULOSCAMPOSLIBRES T0 
						LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO 
					WHERE T1.CODBARRAS='" . $idDelArticulo."'";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
            return $query;

        }
		public function borrarImagenCodarticulo($idDelArticulo,$campo)
        {
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="UPDATE ARTICULOSCAMPOSLIBRES SET " . $campo . "='' 
					WHERE CODARTICULO='" . $idDelArticulo."'";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
            return $query;

        }
		
		public function setRutaImagenByCodarticulo($idDelArticulo,$campo,$valor)
        {
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="UPDATE ARTICULOSCAMPOSLIBRES SET " . $campo . "='" . $valor . "' 
					WHERE CODARTICULO='" . $idDelArticulo."'";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
            return $query;

        }
		public function actualizarImagenes($idDelArticulo){
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="UPDATE ARTICULOSCAMPOSLIBRES SET ES_ACTUALIZAR_IMAGEN='T' FROM ARTICULOSCAMPOSLIBRES T0 LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO WHERE T1.CODBARRAS='$idDelArticulo'";
			$stmt = $conn->prepare($query); 
            $stmt->execute();

		}
		/*
		cambiar el order de las fotos
		@idDelArticulo codigo del articulo al que se le van a cambiar las fotos
		@actualiza cadena con el numero de las fotos que vamos a cambiar por otra ejemplo
		ES_IMAGE1=ES_IMAGE2, ES_IMAGE3=ES_IMAGE4
		*/
		public function ordenImagenes($idDelArticulo,$actualiza){
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="UPDATE ARTICULOSCAMPOSLIBRES SET ES_ACTUALIZAR_IMAGEN='T' ". $actualiza ." WHERE CODARTICULO=$idDelArticulo";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
			return $query;

		}
		/*
		cambiar el order de las fotos
		@idDelArticulo codigo del articulo al que se le van a cambiar las fotos
		@actualiza cadena con el numero de las fotos que vamos a cambiar por otra ejemplo
		ES_IMAGE1=ES_IMAGE2, ES_IMAGE3=ES_IMAGE4
		*/
		public function getAllFotos($idDelArticulo){
			$server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);

            $query="SELECT ES_IMAGE1, ES_IMAGE2, ES_IMAGE3, ES_IMAGE4, ES_IMAGE5, ES_IMAGE6, ES_IMAGE7 FROM ARTICULOSCAMPOSLIBRES WHERE CODARTICULO=$idDelArticulo";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
			return $stmt->fetchAll();
		}
		public function publicar($idDelArticulo){
			$server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			
			$query="UPDATE ARTICULOSCAMPOSLIBRES SET ES_OUTLET='T', ES_SALE='F' FROM ARTICULOS T0 
LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
LEFT OUTER JOIN ARTICULOSLIN T3 ON T0.CODARTICULO=T3.CODARTICULO
WHERE T3.CODBARRAS='".$idDelArticulo."' AND VISIBLE_WEB='T'";
			$hoy=Date("Y/m/d");
			$query2="UPDATE ARTICULOSCAMPOSLIBRES SET ES_SALE='F', ES_OUTLET='T' 
	FROM ARTICULOS T0
	LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
	LEFT OUTER JOIN ARTICULOSLIN T3 ON T0.CODARTICULO=T3.CODARTICULO
	LEFT OUTER JOIN PRECIOSVENTA T12 ON T12.IDTARIFAV = 4 AND T12.CODARTICULO = T0.CODARTICULO
	LEFT OUTER JOIN PRECIOSVENTA T13 ON T13.IDTARIFAV = 8 AND T13.CODARTICULO = T0.CODARTICULO
	WHERE T1.VISIBLE_WEB='F' AND T0.TACON='SI' AND T0.DESCATALOGADO='F' AND T12.PNETO2 IS NOT NULL AND T12.PNETO2<>'' AND T12.PNETO2<>0 AND T12.PNETO2 "."<"." T12.PNETO AND T12.DESDE2 < ".$hoy." AND T12.HASTA2"." > ".$hoy." AND T3.CODBARRAS='".$idDelArticulo."'";
																		
			$query3="UPDATE ARTICULOSCAMPOSLIBRES SET ES_CATEGORIAS=T2.CATEGORY FROM ARTICULOS T0 
LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
LEFT OUTER JOIN EXPORTADOR_ELITESTORE.dbo.TEMP_CATEGORIAS T2 ON T0.DPTO=T2.DPTO AND T0.SECCION=T2.SEC AND T0.FAMILIA=T2.FAM AND T1.ES_SALE=T2.SALE AND T1.ES_OUTLET=T2.OUTLET
LEFT OUTER JOIN ARTICULOSLIN T3 ON T0.CODARTICULO=T3.CODARTICULO
WHERE T3.CODBARRAS='".$idDelArticulo."'";
			$currentdate=date('Y-m-d');
			$query4="UPDATE ARTICULOS SET TACON='SI' FROM ARTICULOS T0 LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO WHERE T1.CODBARRAS='$idDelArticulo'";
			$query5="UPDATE ARTICULOSCAMPOSLIBRES SET FECHA_SUBIDA='$currentdate' FROM ARTICULOSCAMPOSLIBRES T0 LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO WHERE T1.CODBARRAS='$idDelArticulo'";
			$stmt = $conn->prepare($query); 
            $stmt->execute();
			echo "Actualizando Outlet";
			echo "<hr>";
			$stmt = $conn->prepare($query2); 
            $stmt->execute();
			echo "Actualizando Sale";
			echo "<hr>";
			$stmt = $conn->prepare($query3); 
            $stmt->execute();
			echo "Actualizando Categorias";
			echo "<hr>";
			// Comprobar tacon=si, si no almaceno el codigo y ejecuto query 4.
            $querySelect="SELECT T0.TACON FROM ARTICULOS T0 LEFT OUTER JOIN ARTICULOSLIN T1 ON T0.CODARTICULO=T1.CODARTICULO WHERE T1.CODBARRAS = '$idDelArticulo' AND T0.TACON='NO'";
            $stmt = $conn->prepare($querySelect); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if(count($result)){
                $stmt = $conn->prepare($query5); 
				$stmt->execute();
				$stmt = $conn->prepare($query4); 
				$stmt->execute();
            }
			
			echo "Poniendo Visible Web";
			echo "<hr>";
		}
        public function getAllMarcas(){
            $server= $this->server;
            $dataBaseManager= $this->dataBaseManager;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
            $query="SELECT CODMARCA, DESCRIPCION FROM MARCA";
            $stmt = $conn->prepare($query); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if ($result === false){
                throw new Exception("La consulta devuelve False");
            }
            return json_encode ($result);
        }
        public function getAllCategorias(){
            $server= $this->server;
            $dataBaseTemp= $this->dataBaseTemp;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
            $query="SELECT * FROM CATEGORIASMAGENTO";
            $stmt = $conn->prepare($query); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if ($result === false){
                throw new Exception("La consulta devuelve False");
            }
            return json_encode ($result);
        }
        public function getAllDesignerLine(){
            $server= $this->server;
            $dataBaseTemp= $this->dataBaseTemp;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
            $query="SELECT * FROM DESIGNERLINE";
            $stmt = $conn->prepare($query); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if ($result === false){
                throw new Exception("La consulta devuelve False");
            }
            return json_encode ($result);
        }
        public function getAllEstilismos(){
            $server= $this->server;
            $dataBaseTemp= $this->dataBaseTemp;
            $uid=$this->uid;
            $pwd=$this->pwd;
            $conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseTemp", $uid, $pwd);
            $query="SELECT * FROM ESTILISMOS";
            $stmt = $conn->prepare($query); 
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_BOTH);
            if ($result === false){
                throw new Exception("La consulta devuelve False");
            }
            return json_encode ($result);
        }
		
	
	}