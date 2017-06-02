<?php

class ManejadorFicheros{
	function getFotos($dir){
		$codbarras=array();
		if (is_dir($dir)) {
			if ($da = opendir($dir)) {
				while (($archivo = readdir($da)) !== false) {
					$codbarras[substr($archivo,0,-7)][]=$archivo;
					
				}
			}
			unset($codbarras[0]);
			unset($codbarras['Th']);
			closedir($da);
		}
		return $codbarras;
	}
	
	function getFotosPorArticulo($directorio_fotos,$codarticulo){
		$fotos=array();
		if (is_dir($directorio_fotos)) {
			if ($da = opendir($directorio_fotos)) {
				while (($archivo = readdir($da)) !== false) {
					if (strpos($archivo,$codarticulo)!== FALSE){
						$fotos[]=$archivo;
					}
				}
			}
			closedir($da);
		}
		return $fotos;
	}
	
	
}