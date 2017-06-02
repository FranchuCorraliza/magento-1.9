
<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])) {

    include'./html/header.php';
	include'./html/cabeceraDescripciones.php';
	
	if ($_GET){
		$query="SELECT DISTINCT T0.CODARTICULO AS CODIGO, T3.DESCRIPCION AS MARCA, T0.TIPO AS TIPO, T0.TEMPORADA AS TEMPORADA, T0.REFPROVEEDOR AS REFERENCIA, T0.DESCRIPCION AS NOMBRE, T1.CAMPO1 AS NOMBRE_EN, T1.CAMPO3 AS DESCRIPCION, T1.CAMPO6 AS DESCRIPCION_EN, T1.ES_HOME_PIC AS ES_HOME_PIC,T1.ES_UNISEX AS ES_UNISEX,T1.ES_RUNAWAY AS ES_RUNWAY, T1.ES_ESTILISMOS AS ES_ESTILISMOS, T1.ES_TAGS AS ES_TAGS
					FROM ARTICULOS T0 
					LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
					LEFT OUTER JOIN ALBCOMPRALIN T2 ON T0.CODARTICULO=T2.CODARTICULO
					LEFT OUTER JOIN MARCA T3 ON T0.MARCA=T3.CODMARCA
					LEFT OUTER JOIN ARTICULOSLIN T4 ON T0.CODARTICULO=T4.CODARTICULO";
		$where=array();
		if(isset($_GET['albaran'])){
			if($_GET['albaran']!=""){
				$albaran=explode(' ',$_GET['albaran']);
				if(count($albaran)==2){
					$where[]="T2.NUMSERIE='".$albaran[0]."' AND T2.NUMALBARAN=".$albaran[1];
				}
			}
			
		}
		if(isset($_GET['marca'])!=""){
			if($_GET['marca']!=""){
				$where[]="T3.DESCRIPCION LIKE '".$_GET['marca']."'";
			}
		}
		if(isset($_GET['referencia'])){
			if($_GET['referencia']!=""){
				$where[]="T0.REFPROVEEDOR='".$_GET['referencia']."'";
			}
		}
		if($_GET['nombre']!=""){
			$where[]="T0.DESCRIPCION LIKE '".$_GET['nombre']."'";
		}
		if(isset($_GET['tipo'])){
			if($_GET['tipo']!=""){
				$where[]="T0.TIPO=".$_GET['tipo'];
			}
		}
		if(isset($_GET['temporada'])){
			if($_GET['temporada']!=""){
				$where[]="T0.TEMPORADA='".$_GET['temporada']."'";
			}
		}
		if(isset($_GET['editor_pics']) && $_GET['editor_pics']==1){
			$where[]="T1.ES_HOME_PIC='T'";
		}
		if(isset($_GET['instock']) && $_GET['instock']==1){
			$where[]="(isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO and len(codalmacen)>=2 AND (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0))>=1";
		}
		if(isset($_GET['publicado']) && $_GET['publicado']==1){
			$where[]="T0.TACON='SI'";
		}
		if(isset($_GET['codigo']) && $_GET['codigo']!=""){
			$where[]="T4.CODBARRAS='".$_GET['codigo']."'";
		}
		
		if (count($where)>=1){
			
			$where=implode(" AND ",$where);
			$query.=" WHERE ".$where;
		
			$server= "192.168.1.201";
			$dataBaseManager= "G001";
			$uid="ICGAdmin";
			$pwd="masterkey";
			$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
			$stmt = $conn->prepare($query); 
			$stmt->execute();
			$result = $stmt->fetchall(PDO::FETCH_BOTH);
			if ($result === false){
				throw new Exception("La consulta devuelve False");
			}
			
			$html="";
			$recuento=count($result);
			echo "<hr><strong>$recuento items que concuerdan</strong><hr>";
			foreach ($result as $line){
				$homePic='';
				if ($line['ES_HOME_PIC']=='T'){
					$homePic='checked';					
				}
				$unisex='';
				if ($line['ES_UNISEX']=='T'){
					$unisex='checked';					
				}
				$runway='';
				if ($line['ES_RUNWAY']=='T'){
					$runway='checked';
				}
				$selectLE='';
				$selectR='';
				$selectOO='';
				if (in_array('LIMITED EDITION',explode(',',$line['ES_TAGS']))){
					$selectLE='selected';
				}
				if (in_array('RUNWAY',explode(',',$line['ES_TAGS']))){
					$selectR='selected';
				}
				if (in_array('ONLINE ONLY',explode(',',$line['ES_TAGS']))){
					$selectOO='selected';
				}
				echo"<form class='formulario' enctype='multipart/form-data' action='./ajax/guardarDescripcion.php' method='post' target='_blank'><table style='margin-top:50px' class='table table-striped'><tbody><form class='formulario'><tr>
							<td colspan=4>
								<table style='width:100%'>
									<tr>
										<th>CODIGO</th><th>MARCA</th><th>TIPO</th><th>TEMPORADA</th><th>REFERENCIA</th>
									</tr>
									<tr>
										<td>".$line['CODIGO']."</td><td>".$line['MARCA']."</td><td>".$line['TIPO']."</td><td>".$line['TEMPORADA']."</td><td>".$line['REFERENCIA']."</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<th>NOMBRE</th><th <th>NOMBRE INGLÉS</th>
						</tr>
						<tr>
							<td ><input class='form-control' type='text' name='nombre' value=\"".$line['NOMBRE']."\"/></td>
							<td ><input class='form-control' type='text' name='nombre_en' value=\"".$line['NOMBRE_EN']."\"/></td>
						</tr>
						<tr>
							<th >DESCRIPCION</th><th >DESCRIPCION INGLÉS</th>
						</tr>
						<tr>
							<td><textarea class='form-control' name=\"descripcion\" >".$line['DESCRIPCION']."</textarea></td>
							<td><textarea class='form-control' name=\"descripcion_en\" >".$line['DESCRIPCION_EN']."</textarea></td>
						</tr>
						<tr>
							<td colspan=5>
								<table style='width:100%'>
									<tr>
										<th>EDITOR PIC</th><th>UNISEX</th><th>RUNWAY</th><th>ESTILISMOS</th><th>TAGS</th>
									</tr>
									<tr>
										<td><input type='checkbox' name='home_pic' value='1' $homePic></td>
										<td><input type='checkbox' name='unisex' value='1' $unisex></td>
										<td><input type='checkbox' name='runway' value='1' $runway></td>
										<td><input class='form-control' type='text' name='estilismos' value=\"".$line['ES_ESTILISMOS']."\"/></td>
										<td><select class='form-control' name='tags[]' id='tags' multiple='multiple'>
												<option value='LIMITED EDITION' $selectLE>LIMITED EDITION</option>
												<option value='RUNWAY' $selectR>RUNWAY</option>
												<option value='ONLINE ONLY' $selectOO>ONLINE ONLY</option>
											</select>
											
										</td>
										<td><button type='button' class='btn btn-info btn-lg' data-toggle='modal' data-target='#myModal-".$line['CODIGO']."'>Categorias</button>
											<div id='myModal-".$line['CODIGO']."' class='modal fade' role='dialog'>
											  <div class='modal-dialog'>

												<!-- Modal content-->
												<form id='categorias-".$line['CODIGO']."'>
													<div class='modal-content'>
													
													  <div class='modal-header'>
														<button type='button' class='close' data-dismiss='modal'>&times;</button>
														<h4 class='modal-title'>Categorias</h4>
													  </div>
													  <div class='modal-body'>
														".getCategoryForm($line['CODIGO'])."
													  </div>
													  <div class='modal-footer'>
														<button type='submit' class='btn btn-default' data-dismiss='modal'>Guardar</button>
														<button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
													  </div>
													</div>
												</form>

											  </div>
											</div>
										</td>
											
									</tr>
								</table>
							</td>
						</tr>
						<tr style='text-align: center;height: 100px;'>
							<td colspan=4>
							<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
							<button type='submit' class='btn btn-primary' id='guardar".$line['CODIGO']."'>Guardar</button><div id='msg'></div></td>
							
						</tr><tr></tr></tbody></table></form>";				
			}
			echo"";
			echo $html;
		}else{
			echo "Debe filtrar al menos por uno de los campos disponibles.<hr><button class='btn btn-primary' onclick='history.back()' >Volver</button>";
		}
		
	}else{
			echo "Debe filtrar al menos por uno de los campos disponibles.<hr><button class='btn btn-primary' onclick='history.back()' >Volver</button>";
		}
		if(isset($_SESSION['mensajes'])){
        echo $_SESSION['mensajes'];
        session_unset('mensajes');
    }

    //include'./html/paneles.php';
    ?>
    </div>
    <?php
    include './html/sidebar.php';
    ?>
    <!-- /.row -->
    </div>
    <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <?php
    include './html/footer.php';
		}
else{
    $_SESSION['mensajes']="Usuario incorrecto";
    echo '<script type="text/javascript" language="JavaScript">location.href = "../index.php";</script>';
}	
		
function getCategoryForm($codigo){
	$server= "192.168.1.201";
	$dataBaseManager= "EXPORTADOR_ELITESTORE";
	$uid="ICGAdmin";
	$pwd="masterkey";
	$conn = new PDO( "sqlsrv:server=$server ; Database = $dataBaseManager", $uid, $pwd);
	$query="SELECT * FROM CATEGORIASMAGENTO";
	$query2="SELECT ES_CATEGORIAS FROM G001.dbo.ARTICULOSCAMPOSLIBRES WHERE CODARTICULO=".$codigo;
	try{
		$stmt = $conn->prepare($query); 
		$stmt->execute();
		$categorias = $stmt->fetchall(PDO::FETCH_BOTH);
		$stmt = $conn->prepare($query2); 
		$stmt->execute();
		$categoriasSeleccionadas = $stmt->fetchall(PDO::FETCH_BOTH);
	}catch(Exception $e){
		return $e;
	}
	$arbol=getArbol($categorias);
	//var_dump($arbol);
	$catSel=array();
	if ($categoriasSeleccionadas){
		$catSel=explode(',',$categoriasSeleccionadas[0]['ES_CATEGORIAS']);
	}
	$html="<div class='tree well'>";
	$html.= dibujarArbol($arbol,$catSel);
	$html.="</div>";
	return $html;
	//echo $html;
	
}		

function getArbol($categorias){
	$arbol=array();
	//Level1
	foreach ($categorias as $categoria){
		$path=explode('/',$categoria['PATH']);
		if(count($path)==1){
			$arbol[$path[0]]=array('id'=>$categoria['CATEGORY_ID'],'name'=>$path[0],'hijos'=>array());
		}
	}
	//Level2
	foreach ($categorias as $categoria){
		$path=explode('/',$categoria['PATH']);
		if(count($path)==2){
			$arbol[$path[0]]['hijos'][$path[1]]=array('id' => $categoria['CATEGORY_ID'], 'name'=>$path[1],'hijos'=>array());
		}			
	}
	//Level3
	foreach ($categorias as $categoria){
		$path=explode('/',$categoria['PATH']);
		if(count($path)==3){
			$arbol[$path[0]]['hijos'][$path[1]]['hijos'][$path[2]]=array('id' => $categoria['CATEGORY_ID'], 'name'=>$path[2],'hijos'=>array());
		}			
	}
	//Level4
	foreach ($categorias as $categoria){
		$path=explode('/',$categoria['PATH']);
		if(count($path)==4){
			$arbol[$path[0]]['hijos'][$path[1]]['hijos'][$path[2]]['hijos'][$path[3]]=array('id' => $categoria['CATEGORY_ID'], 'name'=>$path[3],'hijos'=>array());
		}			
	}
	//Level5
	foreach ($categorias as $categoria){
		$path=explode('/',$categoria['PATH']);
		if(count($path)==5){
			$arbol[$path[0]]['hijos'][$path[1]]['hijos'][$path[2]]['hijos'][$path[3]]['hijos'][$path[4]]=array('id' => $categoria['CATEGORY_ID'], 'name'=>$path[4],'hijos'=>array());
		}			
	}
	//Level6
	foreach ($categorias as $categoria){
		$path=explode('/',$categoria['PATH']);
		if(count($path)==6){
			$arbol[$path[0]]['hijos'][$path[1]]['hijos'][$path[2]]['hijos'][$path[3]]['hijos'][$path[4]]['hijos'][$path[5]]=array('id' => $categoria['CATEGORY_ID'], 'name'=>$path[5],'hijos'=>array());
		}			
	}
	return $arbol;
	
}

function dibujarArbol($arbol,$catSel){
	$html="<ul>";
	foreach ($arbol as $linea){
		$check="";
		if (in_array($linea['id'],$catSel)){
			$check="checked";
		}
		$html.="<li><input type='checkbox' id='check-".$linea['id']."' $check>".$linea['name'];
		if ($linea['hijos']>0){
			$html.=dibujarArbol($linea['hijos'],$catSel);			
		}
		$html.="</li>";
	}	
	$html.="</ul>";	
	return $html;
}
?>