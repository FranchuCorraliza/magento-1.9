<?php

	class SistemaLog{
		private $errorFile="report/error.log";
		private $logFile="report/log.log";
		private $destinatarioEmails="jesus.it@elitespain.es";
		
		public function logear($msg,$error=false){
			if ($error){
				$file=$this->errorFile;
			}else{
				$file=$this->logFile;
			}
			
			$fp = fopen($file, "a");
			$date= date("Y-m-d H:i:s");
			$line=$date.': '.$msg.PHP_EOL;
			fwrite($fp,$line);
			fclose($fp);
		}
		
		public function enviarAlerta($msg){
			$texto="
			Se produjo un error durante el proceso de actualización de stocks en Elite Store.
			El mensaje devuelto por el sistema es:
			$msg
			
			";
			mail($this->destinatarioEmails,"Probando Gestion",$texto);
		}
		
		public function limpiar(){
			$file=$this->logFile;
			$fp = fopen($file, "w");
			fwrite($fp,"");
			fclose($fp);
		}
	}
	