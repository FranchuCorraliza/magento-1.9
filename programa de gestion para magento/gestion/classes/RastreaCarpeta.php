<?php

class RastreaCarpeta{
	
	function getFotos($dir){
		$codbarras=array();
		if (is_dir($dir)) {
			
			if ($da = opendir($dir)) {
			  //leemos del directorio hasta que termine
				while (($archivo = readdir($da)) !== false) {
					/*Si es un directorio imprimimos la ruta
					 * y llamamos recursivamente esta función
					 * para que verifique dentro del nuevo directorio
					 * por mas directorios o archivos
					 */
					$codbarras[substr($archivo,0,-6)][]=$archivo;
					
				}
			}
			//Eliminamos posiciones 1ª y última que corresponden a ficheros de configuración del directorio
			unset($codbarras[0]);
			unset($codbarras['Thu']);
			closedir($da);
		}
		return $codbarras;
		
	}
	
	function pintarTabla($dir,$fotos,$server,$uid,$pwd){
		
		$html="<table  class='table'><tr><th>Codigo</th><th>Referencia</th><th>Nombre</th><th>Descripcion</th><th>Nombre_en</th><th>Descripcion_en</th><th>Precio</th><th>Precio Oferta</th><th>Precio Outlet</th><th>Outlet</th><th>Stock</th><th>Fotos</th><th><input type='checkbox' onClick='toggle(this)' /> Todos</th></tr>";
		$i=0;
		foreach ($fotos as $cod => $fotosArticulo):
					
			$query ="SELECT DISTINCT T0.CODARTICULO AS CODIGO, T1.REFPROVEEDOR AS REFERENCIA, T0.DESCRIPCION AS NOMBRE, T0.DESCRIPADIC AS DESCRIPCION, T2.CAMPO1 AS NOMBRE_ENGLISH, T2.CAMPO3 AS DESCRIPCION_ENGLISH, T4.PNETO AS PRECIO, T4.PNETO2 AS PRECIO_OFERTA, T5.PNETO2 AS PRECIO_OUTLET, T0.VISIBLEWEB AS OUTLET,isnull((select sum(T20.STOCK) from STOCKS T20 WHERE T0.CODARTICULO = T20.CODARTICULO and len(codalmacen)>=2 AND  (CODALMACEN like 'A[0-9]' or CODALMACEN like 'A[0-9][0-9]') and cast(SUBSTRING(Codalmacen,2,len(codalmacen)-1) as int) between 3 and 50),0) AS STOCK  FROM ARTICULOS T0 LEFT JOIN REFERENCIASPROV T1 ON T0.CODARTICULO = T1.CODARTICULO LEFT JOIN ARTICULOSLIN T3 ON T0.CODARTICULO=T3.CODARTICULO LEFT JOIN ARTICULOSCAMPOSLIBRES T2 ON T0.CODARTICULO=T2.CODARTICULO LEFT JOIN PRECIOSVENTA T4 ON T4.IDTARIFAV = 4 AND T4.CODARTICULO = T0.CODARTICULO LEFT JOIN PRECIOSVENTA T5 ON T5.IDTARIFAV = 8 AND T5.CODARTICULO = T0.CODARTICULO WHERE T3.CODBARRAS='$cod'";
			$consulta= new ConexionManager;
			$result=$consulta->getQuery($server, $uid, $pwd, $query);
			foreach ($result as $row):
				$html.="<tr><td>".$row['CODIGO']."</td><td>".$row['REFERENCIA']."</td><td>".$row['NOMBRE']."</td><td>".$row['DESCRIPCION']."</td><td>".$row['NOMBRE_ENGLISH']."</td><td>".$row['DESCRIPCION_ENGLISH']."</td><td>".$row['PRECIO']."</td><td>".$row['PRECIO_OFERTA']."</td><td>".$row['PRECIO_OUTLET']."</td><td>".$row['OUTLET']."</td><td>".$row['STOCK']."</td>";
				$html.="<td>";
				foreach ($fotosArticulo as $foto):
					$html.="<img src='".$dir."resize.php?file=".$foto."&width=50' width='50px'/>";
				endforeach;
				$html.="</td>";
				$html.="<td><input type='checkbox' name='foo$i' value='$cod' class='subido'/></td></tr>";
			endforeach;
			$i++;
		endforeach;
		$html.="</table>";
		return $html;	
	}
}