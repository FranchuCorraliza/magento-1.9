<?php
	class GestionFtp{
		private $ftpDomain = "95.211.78.1";
        private $ftpDomainNas = "192.168.1.20";
		private $ftpusername ="importador-stocks@desarrollo.elitestore.es";
        private $ftpNasusername ="ftpbackup";
		private $ftppass ="&Xjt5u";
        private $ftpNaspass ="Elite2017.";
		private $ftpdir ="processing";
        private $ftpimagedir ="fotosweb";
        private $ftpimageusername ="importador-imagenes@desarrollo.elitestore.es";
        private $ftpimagepass ="Swnb4_";
		
		
		function conectarftp(){
			
			$conn_id = ftp_connect($this->ftpDomain); 

			// iniciar una sesion con nombre de usuario y contraseña
			$login_result = ftp_login($conn_id, $this->ftpusername, $this->ftppass); 

			// verificar la conexión
			if ((!$conn_id) || (!$login_result)) {  
				echo "<p>!La conexión FTP ha fallado!</p>";
				return false;
				exit; 
			}
			ftp_pasv ($conn_id,true);
			return $conn_id;
		}
        function conectarftpimages(){
            
            $conn_id = ftp_connect($this->ftpDomain); 

            // iniciar una sesión con nombre de usuario y contraseña
            $login_result = ftp_login($conn_id, $this->ftpimageusername, $this->ftpimagepass); 

            // verificar la conexion
            if ((!$conn_id) || (!$login_result)) {  
                echo "<p>!La conexión FTP ha fallado!</p>";
                return false;
                exit; 
            }
            ftp_pasv ($conn_id,true);
            return $conn_id;
        }
        function conectarftpNas(){

            $conn_id = ftp_connect($this->ftpDomainNas);

            // iniciar una sesión con nombre de usuario y contraseña
            $login_result = ftp_login($conn_id, $this->ftpNasusername, $this->ftpNaspass);

            // verificar la conexion
            if ((!$conn_id) || (!$login_result)) {
                echo "<p>!La conexión FTP ha fallado!</p>";
                return false;
                exit;
            }
            ftp_pasv ($conn_id,true);
            return $conn_id;
        }
		
		function subirftp($file,$rutaorigen){
			// establecer una conexi�n b�sica
			
			$conn_id=$this->conectarftp();
			// subir un archivo
			$upload = ftp_put($conn_id, $this->ftpdir."/".$file, $rutaorigen."/".$file, FTP_BINARY);  //sube el fichero 
			// comprobar el estado de la subida
			
			if (!$upload) {  
				echo "<p>�La subida FTP ha fallado!</p>";
				return false;
			} else {
				return true;
			}
		}
        function subirimageftp($file,$rutaorigen){
            // establecer una conexión básica
            
            $conn_id=$this->conectarftpimages();
            // subir un archivo
            
            $upload = ftp_put($conn_id, $this->ftpimagedir."/".$file, $rutaorigen, FTP_BINARY);  

            //sube el fichero
            // comprobar el estado de la subida
            
            if (!$upload) {  
                echo "<p>!La subida FTP ha fallado!</p>";
                return false;
            } else {
                $resultado = $this->ftpimagedir."/".$file; 
                return $resultado;
            }
        }
		
		
		function updateimageftp($file,$rutaorigen){
			$conn_id=$this->conectarftpimages();
			$i=1;
			while (ftp_size($conn_id, $this->ftpimagedir."/".$file)>0){ //Buscamos un nombre de fichero que no exista
				if ($i!=1){
					$file=explode("_",$file)[0]."_".$i.".jpg"; //Nos quedamos con el nombre del fichero excepto el indice de updates añadido y la extensión
				}else{
					$file=explode(".",$file)[0]."_".$i.".jpg"; //Nos quedamos con el nombre del fichero excepto la extensión
				}
				$i++;
			}
			$upload = ftp_put($conn_id, $this->ftpimagedir."/".$file, $rutaorigen, FTP_BINARY);  
			if (!$upload) {  
                echo "<p>!La subida FTP ha fallado!</p>";
                return false;
            } else {
                $resultado = $this->ftpimagedir."/".$file; 
                return $resultado;
            }
		}
		
        //funcion para obtener la carpeta que va a contener las fotos subidas hoy
        function getCarpetaBackup(){
            $dir = "/DEPARTAMENTO WEB/BACKUPS-FOTOS/outbox/" . date("Y") . "/outbox-" . date("Y") . "-" . date("m") . "-" . date("d")."/";
            $conn_id=$this->conectarftpNas();
            if (ftp_mkdir($conn_id, $dir)) {
                return $dir;
            } else {
                return $dir;
            }
        }
		//funcion para obtener la carpeta que va a contener las fotos subidas hoy
        function getCarpetaBackupPasarela(){
            $dir = "/DEPARTAMENTO WEB/BACKUPS-FOTOS/pasarela/" . date("Y") . "/outbox-" . date("Y") . "-" . date("m") . "-" . date("d")."/";
            $conn_id=$this->conectarftpNas();
            if (ftp_mkdir($conn_id, $dir)) {
                return $dir;
            } else {
                return $dir;
            }
        }
        //subir imagenes a la carpeta backup
        function subirimagenbackup($file,$rutaorigen,$rutabakup){
            // establecer una conexión básica con el nas

            $conn_id=$this->conectarftpNas();
            // subir un archivo

            $upload = ftp_put($conn_id, $rutabakup.$file, $rutaorigen, FTP_BINARY);

            if (!$upload) {
                echo "<p>1La subida FTP ha fallado!</p>";
                return false;
            } else {
                $resultado = $this->ftpimagedir . "/" . $file;
                return $resultado;
            }
        }
		//subir imagenes a la carpeta backup
        function subirimagenbackuppasarela($file,$rutaorigen,$rutabakup){
            // establecer una conexión básica con el nas

            $conn_id=$this->conectarftpNas();
            // subir un archivo

            $upload = ftp_put($conn_id, $rutabakup.$file, $rutaorigen, FTP_BINARY);

            if (!$upload) {
                echo "<p>1La subida FTP ha fallado!</p>";
                return false;
            } else {
                $resultado = $this->ftpimagedir . "/" . $file;
                return $resultado;
            }
        }
        function getCarpetaftp(){
            // establecer una conexión básica
            
            $conn_id=$this->conectarftpimages();
            // subir un archivo
            
            $upload = ftp_nlist($conn_id, "./fotosweb/");
            
            if (!$upload) {  
                echo "<p>�La subida FTP ha fallado!</p>";
                return false;
            } else {
                return $upload;
            }
        }
		function getCarpetaNoPublicadosftp(){
            // establecer una conexión básica
            
            $conn_id=$this->conectarftpimages();
            // subir un archivo
            
            $upload = ftp_nlist($conn_id, "./nopublicados03042017/");
            
            if (!$upload) {  
                echo "<p>�La subida FTP ha fallado!</p>";
                return false;
            } else {
                return $upload;
            }
        }
		function ficheroExiste($fileBuscado){
			$conn_id=$this->conectarftp();
			
			$files=ftp_nlist($conn_id, $this->ftpdir);
			if (in_array($this->ftpdir.'/'.$fileBuscado,$files)){
				return true;
			}else{
				return false;
			}
		}
		function borrarFicherosStocks(){
			$conn_id=$this->conectarftp();
			
			$files = ftp_nlist($conn_id, $this->ftpdir);
			if (in_array($this->ftpdir.'/'."result-stocks.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-stocks.log");
			}
			if (in_array($this->ftpdir.'/'."result-stocks-conf.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-stocks-conf.log");
			}
			if (in_array($this->ftpdir.'/'."stocks.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/stocks.csv");
			}
			if (in_array($this->ftpdir.'/'."stocksConf.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/stocksConf.csv");
			}
		}
		
		function borrarFicherosBase(){
			$conn_id=$this->conectarftp();
			$files = ftp_nlist($conn_id, $this->ftpdir);
			if (in_array($this->ftpdir.'/'."result-base.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-base.log");
			}
			if (in_array($this->ftpdir.'/'."base.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/base.csv");
			}
		}
		
		function borrarFicherosPrecio(){
			$conn_id=$this->conectarftp();
			$files = ftp_nlist($conn_id, $this->ftpdir);
			if (in_array($this->ftpdir.'/'."result-precio.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-precio.log");
			}
			if (in_array($this->ftpdir.'/'."precio.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/precio.csv");
			}
		}
		
		function borrarFicherosIngles(){
			$conn_id=$this->conectarftp();
			$files = ftp_nlist($conn_id, $this->ftpdir);
			if (in_array($this->ftpdir.'/'."result-ingles.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-ingles.log");
			}
			if (in_array($this->ftpdir.'/'."ingles.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/ingles.csv");
			}
		}
		function borrarFicherosNuevos(){
			$conn_id=$this->conectarftp();
			$files = ftp_nlist($conn_id, $this->ftpdir);
			if (in_array($this->ftpdir.'/'."result-nuevo.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-nuevo.log");
			}
			if (in_array($this->ftpdir.'/'."nuevo.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/nuevos.csv");
			}
			if (in_array($this->ftpdir.'/'."result-ingles.log",$files)){
				ftp_delete($conn_id,$this->ftpdir."/result-ingles.log");
			}
			if (in_array($this->ftpdir.'/'."ingles.csv",$files)){
				ftp_delete($conn_id,$this->ftpdir."/ingles.csv");
			}
		}
	}