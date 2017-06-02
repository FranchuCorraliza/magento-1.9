<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])) {
    include'./html/header.php';
    ?>

    <?php
	include'./html/cabecerasubirCambiarFotos.php';

	if ($_GET){
		$query="SELECT DISTINCT T0.CODARTICULO AS CODIGO,
		T3.DESCRIPCION AS MARCA,
		T0.TIPO AS TIPO,
		T0.TEMPORADA AS TEMPORADA,
		T0.REFPROVEEDOR AS REFERENCIA,
		T0.DESCRIPCION AS NOMBRE,
		T1.CAMPO1 AS NOMBRE_EN,
		T1.CAMPO3 AS DESCRIPCION,
		T1.CAMPO6 AS DESCRIPCION_EN,
		T1.ES_HOME_PIC AS ES_HOME_PIC,
		T1.ES_UNISEX AS ES_UNISEX,
		T1.ES_RUNAWAY AS ES_RUNWAY,
		T1.ES_ESTILISMOS AS ES_ESTILISMOS,
		T1.ES_IMAGE1 AS ES_IMAGE1,
		T1.ES_IMAGE2 AS ES_IMAGE2,
		T1.ES_IMAGE3 AS ES_IMAGE3,
		T1.ES_IMAGE4 AS ES_IMAGE4,
		T1.ES_IMAGE5 AS ES_IMAGE5,
		T1.ES_IMAGE6 AS ES_IMAGE6,
		T1.ES_IMAGE7 AS ES_IMAGE7,
		T1.ES_TAGS AS ES_TAGS
					FROM ARTICULOS T0 
					LEFT OUTER JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO
					LEFT OUTER JOIN ALBCOMPRALIN T2 ON T0.CODARTICULO=T2.CODARTICULO
					LEFT OUTER JOIN MARCA T3 ON T0.MARCA=T3.CODMARCA
					LEFT OUTER JOIN ARTICULOSLIN T4 ON T0.CODARTICULO=T4.CODARTICULO";
		$where=array();
		if($_GET['albaran']!=""){
			$albaran=explode(' ',$_GET['albaran']);
			if(count($albaran)==2){
				$where[]="T2.NUMSERIE='".$albaran[0]."' AND T2.NUMALBARAN=".$albaran[1];
			}
		}
		if($_GET['marca']!=""){
			$where[]="T3.DESCRIPCION LIKE '".$_GET['marca']."'";
		}
		if($_GET['referencia']!=""){
			$where[]="T0.REFPROVEEDOR='".$_GET['referencia']."'";
		}
		if($_GET['nombre']!=""){
			$where[]="T0.DESCRIPCION LIKE '".$_GET['nombre']."'";
		}
		if($_GET['tipo']!=""){
			$where[]="T0.TIPO=".$_GET['tipo'];
		}
		if($_GET['temporada']!=""){
			$where[]="T0.TEMPORADA='".$_GET['temporada']."'";
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
				
				$html.="<form class='formulario'><table style='margin-top:50px' class='table table-striped'><tbody><form class='formulario'><tr>
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
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE1']." style='width:71px;' /><br/>
								<input type='file' name='myImage' accept='image/*' /><br/>
								<label>Orden</label><br/>
								<input type='increment' value='1'  style='width:71px;'/><br/>
							</td>
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE2']." style='width:71px;' /><br/>
								<input type='file' name='foto1'><br/>
								<label>Orden</label><br/>
								<input type='increment' value='2'  style='width:71px;'/><br/>
							</td>
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE3']." style='width:71px;' /><br/>
								<input type='file' name='foto1'><br/>
								<label>Orden</label><br/>
								<input type='increment' value='3'  style='width:71px;'/><br/>
							</td>
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE4']." style='width:71px;' /><br/>
								<button class='btn btn-primary'>Sustituir</button><br/>
								<label>Orden</label><br/>
								<input type='increment' value='4'  style='width:71px;'/><br/>
							</td>
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE5']." style='width:71px;' /><br/>
								<button class='btn btn-primary'>Sustituir</button><br/>
								<label>Orden</label><br/>
								<input type='increment' value='5'  style='width:71px;'/><br/>
							</td>
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE6']." style='width:71px;' /><br/>
								<button class='btn btn-primary'>Sustituir</button><br/>
								<label>Orden</label><br/>
								<input type='increment' value='6'  style='width:71px;'/><br/>
							</td>
							<td>
								<img src=https://www.elitestores.com/media/import/".$line['ES_IMAGE7']." style='width:71px;' /><br/>
								<button class='btn btn-primary'>Sustituir</button><br/>
								<label>Orden</label><br/>
								<input type='increment' value='7'  style='width:71px;'/><br/>
							</td>
						</tr>
						<tr style='text-align: center;height: 100px;'>
							<td colspan=4>
							<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
							<button type='submit' class='btn btn-primary' id='guardar".$line['CODIGO']."'>Guardar</button><div id='msg'></div></td>
							
						</tr><tr></tr></tbody></table></form>";				
			}
			$html.="";
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
?>