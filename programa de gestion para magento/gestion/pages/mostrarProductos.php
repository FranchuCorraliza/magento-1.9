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
			$where[]="T3.DESCRIPCION LIKE '".strtoupper($_GET['marca'])."'";
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
			echo "<a href='./cambiarfoto.php'>Volver</a>";
			echo "<hr><strong>$recuento items que concuerdan</strong><hr>";
			foreach ($result as $line){
				$imagen1="";
				$imagen2="";
				$imagen3="";
				$imagen4="";
				$imagen5="";
				$imagen6="";
				$imagen7="";
				$imagenDefecto="desaparecido.jpg";
			
				if($line['ES_IMAGE1']!=""){
					$imagen1=$line['ES_IMAGE1'];
				}
				else{
					$imagen1=$imagenDefecto;
				}
				if($line['ES_IMAGE2']!=""){
					$imagen2=$line['ES_IMAGE2'];
				}
				else{
					$imagen2=$imagenDefecto;
				}
				if($line['ES_IMAGE3']!=""){
					$imagen3=$line['ES_IMAGE3'];
				}
				else{
					$imagen3=$imagenDefecto;
				}
				if($line['ES_IMAGE4']!=""){
					$imagen4=$line['ES_IMAGE4'];
				}
				else{
					$imagen4=$imagenDefecto;
				}
				if($line['ES_IMAGE5']!=""){
					$imagen5=$line['ES_IMAGE5'];
				}
				else{
					$imagen5=$imagenDefecto;
				}
				if($line['ES_IMAGE6']!=""){
					$imagen6=$line['ES_IMAGE6'];
				}
				else{
					$imagen6=$imagenDefecto;
				}
				if($line['ES_IMAGE7']!=""){
					$imagen7=$line['ES_IMAGE7'];
				}
				else{
					$imagen7=$imagenDefecto;
				}
				
				$html.="<table style='margin-top:50px' class='table table-striped'><tbody><tr>
							<td colspan=7>
								<table style='width:100%'>
									<tr>
										<th>CODIGO</th><th>MARCA</th><th>TIPO</th><th>TEMPORADA</th><th>REFERENCIA</th><th>NOMBRE</th><th></th>
									</tr>
									<tr>
										<td>".$line['CODIGO']."</td><td>".$line['MARCA']."</td><td>".$line['TIPO']."</td><td>".$line['TEMPORADA']."</td><td>".$line['REFERENCIA']."</td><td>".$line['NOMBRE']."</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto1'>Eliminar</button><br/>
								<form id='formImage1".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen1." style='width:71px;'  id='".$line['CODIGO']."-image1'/><br/>
									<input type='file' name='foto1' name='foto1' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='1'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='1'  style='width:71px;' class='incrementar' id='imagen1'/><br/>
								
							</td>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto2'>Eliminar</button><br/>
								<form id='formImage2".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen2." style='width:71px;'  id='".$line['CODIGO']."-image2' /><br/>
									<input type='file' name='foto2' name='foto2' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='2'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='2'  style='width:71px;' class='incrementar' id='imagen2'/><br/>
								
							</td>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto3'>Eliminar</button><br/>
								<form id='formImage3".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen3." style='width:71px;'  id='".$line['CODIGO']."-image3' /><br/>
									<input type='file' name='foto3' name='foto3' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='3'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='3'  style='width:71px;' class='incrementar' id='imagen3'/><br/>
								
							</td>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto4'>Eliminar</button><br/>
								<form id='formImage4".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen4." style='width:71px;'  id='".$line['CODIGO']."-image4' /><br/>
									<input type='file' name='foto4' name='foto4' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='4'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='4'  style='width:71px;' class='incrementar' id='imagen4'/><br/>
								
							</td>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto5'>Eliminar</button><br/>
								<form id='formImage5".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen5." style='width:71px;'  id='".$line['CODIGO']."-image5' /><br/>
									<input type='file' name='foto5' name='foto5' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='5'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='5'  style='width:71px;' class='incrementar' id='imagen5'/><br/>
								
							</td>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto6'>Eliminar</button><br/>
								<form id='formImage6".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen6." style='width:71px;'  id='".$line['CODIGO']."-image6' /><br/>
									<input type='file' name='foto6' id='foto6' aria-describedby='fileHelp' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='6'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='6'  style='width:71px;' class='incrementar' id='imagen6'/><br/>
								
							</td>
							<td>
								<button type='submit' class='btn btn-danger elimininar' id='".$line['CODIGO']."eliminarfoto7'>Eliminar</button><br/>
								<form id='formImage7".$line['CODIGO']."' enctype='multipart/form-data' action='./ajax/imagen-ajax.php' method='post' target='_blank'>
									<img src=https://www.elitestores.com/media/import/".$imagen7." style='width:71px;'  id='".$line['CODIGO']."-image7' /><br/>
									<input type='file' name='foto7' id='foto7' aria-describedby='fileHelp' style='width:100%'><br/>
									<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
									<input type='hidden' id='foto' name='foto' value='7'/>
									<button type='submit' class='btn btn-primary sustituir'>Sustituir</button><br/>
									<div class='msg'></div>
								</form>
								
								<label>Orden</label><br/>
								<input type='increment' value='7'  style='width:71px;' class='incrementar' id='imagen7'/><br/>
								
							</td>
						</tr>
						<tr style='text-align: center;height: 100px;'>
							<td colspan=7>
							<form class='formcambiarorder'>
								<input type='hidden' id='codigo' name='codigo' value='".$line['CODIGO']."'/>
								
								<input type='hidden' id='".$line['CODIGO']."cambiarimage1' class='cambiarimage' name='".$line['CODIGO']."cambiarimage1' value=''/>
								<input type='hidden' id='".$line['CODIGO']."cambiarimage2' class='cambiarimage' name='".$line['CODIGO']."cambiarimage2' value=''/>
								<input type='hidden' id='".$line['CODIGO']."cambiarimage3' class='cambiarimage' name='".$line['CODIGO']."cambiarimage3' value=''/>
								<input type='hidden' id='".$line['CODIGO']."cambiarimage4' class='cambiarimage' name='".$line['CODIGO']."cambiarimage4' value=''/>
								<input type='hidden' id='".$line['CODIGO']."cambiarimage5' class='cambiarimage' name='".$line['CODIGO']."cambiarimage5' value=''/>
								<input type='hidden' id='".$line['CODIGO']."cambiarimage6' class='cambiarimage' name='".$line['CODIGO']."cambiarimage6' value=''/>
								<input type='hidden' id='".$line['CODIGO']."cambiarimage7' class='cambiarimage' name='".$line['CODIGO']."cambiarimage7' value=''/>
								
								<button type='submit' class='btn btn-primary' id='guardar".$line['CODIGO']."'>Guardar</button>
							</form>
							
							<div id='msg'></div></td>
							
						</tr><tr></tr></tbody></table>";				
			}
			$html.="<script></script>";
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