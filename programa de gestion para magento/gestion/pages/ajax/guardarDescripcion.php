<?php
/*SELECT T0.CODARTICULO AS CODIGO, T3.DESCRIPCION AS MARCA, T0.TIPO AS TIPO, T0.TEMPORADA AS TEMPORADA, T0.REFPROVEEDOR AS REFERENCIA, T0.DESCRIPCION AS NOMBRE, T1.CAMPO1 AS NOMBRE_EN, T1.CAMPO3 AS DESCRIPCION, T1.CAMPO6 AS DESCRIPCION_EN
					FROM ARTICULOS T0 
					LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
					LEFT OUTER JOIN ALBCOMPRALIN T2 ON T0.CODARTICULO=T2.CODARTICULO
					LEFT OUTER JOIN MARCA T3 ON T0.MARCA=T3.CODMARCA";
					
UPDATE ARTICULOSCAMPOSLIBRES 
SET CAMPO1=$_POST['nombre_en'],CAMPO3=$_POST['descripcion'],CAMPO6=$_POST['descripcion_en']
WHERE CODARTICULO=$_POST[$codigo];					
*/
	$query=array();
	if ($_POST){
		if($_POST['codigo']){
			$codigo=$_POST['codigo'];
			$where=" WHERE CODARTICULO=".$_POST['codigo'];
			$set="SET";
			$caracteres_prohibidos = array("'",";");    
			$caracteres_sustitutos = array ("''","");
			if (isset($_POST['home_pic']) && $_POST['home_pic']==1){
				$homePic='T';
			}else{
				$homePic='F';
			}
			if (isset($_POST['unisex']) && $_POST['unisex']==1){
				$unisex='T';
			}else{
				$unisex='F';
			}
			if (isset($_POST['runway']) && $_POST['runway']==1){
				$runway='T';
			}else{
				$runway='F';
			}
			$tags='';
			if (isset($_POST['tags']) && count($_POST['tags'])>0){
				$tags=implode(',',$_POST['tags']);
			}
			
			$query[]="UPDATE ARTICULOS SET DESCRIPCION='".str_replace($caracteres_prohibidos,$caracteres_sustitutos,$_POST['nombre'])."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET CAMPO1='".str_replace($caracteres_prohibidos,$caracteres_sustitutos,$_POST['nombre_en'])."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET CAMPO3='".str_replace($caracteres_prohibidos,$caracteres_sustitutos,$_POST['descripcion'])."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET CAMPO6='".str_replace($caracteres_prohibidos,$caracteres_sustitutos,$_POST['descripcion_en'])."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET ES_HOME_PIC='".$homePic."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET ES_UNISEX='".$unisex."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET ES_RUNAWAY='".$runway."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET ES_TAGS='".str_replace($caracteres_prohibidos,$caracteres_sustitutos,$tags)."'".$where;
			$query[]="UPDATE ARTICULOSCAMPOSLIBRES SET ES_ESTILISMOS='".str_replace($caracteres_prohibidos,$caracteres_sustitutos,$_POST['estilismos'])."'".$where;
			$server= "192.168.1.201";
			$dataBaseManager= "G001";
			$uid="ICGAdmin";
			$pwd="masterkey";
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			try{
				foreach($query as $consulta){
					$stmt = $conn->prepare($consulta); 
					$stmt->execute();
				}
				$msg="<span class='glyphicon glyphicon-ok'></span> Guardado";
			}catch(Exception $e){
				$msg="<span class='glyphicon glyphicon-error'></span> Error";
			}
			
		}else{
			$msg="<span class='glyphicon glyphicon-error'></span> Error";
		}
	}else{
		$msg="<span class='glyphicon glyphicon-error'></span> Error";
	}
	echo "[{\"msg\": \"".$msg."\",	\"codigo\": \"".$codigo."\" }]";
?>