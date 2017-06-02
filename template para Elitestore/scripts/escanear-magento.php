<?php
ob_end_clean();
function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            //$results[] = $path;
        }
    }

    return $results;
}

$listadoPaginasWeb = getDirContents('../app/design/frontend');

// What to look for
$search = '->__(';
$valoresUnicos = array();
// Read from file
foreach ($listadoPaginasWeb as $pagina) {
$lines = file($pagina);
  foreach($lines as $line)
  {
    // Check if the line contains the string we're looking for, and print if it does
    if(strpos($line, $search) !== false)
    {
      //var_dump($line);
      //echo "<hr> maruchi: " . $pagina . " separado ". $line;
      $ocurrencias = explode($search, $line);
      unset($ocurrencias[0]);
      foreach ($ocurrencias as $stringBuscado) {
        $comillas = $stringBuscado[0];
        $stringFinal = explode($comillas . ")", $stringBuscado)[0];
        $stringFinal = substr($stringFinal,1);
        $valoresUnicos[] = $stringFinal;
        flush();
      }
    }
  }
}

$valoresUnicos = array_unique($valoresUnicos);
$write = fopen('translate-frontend.csv', 'w');

foreach ($valoresUnicos as $traduccion) {
  fputcsv ($write, array($traduccion), ",","\"");
}
fclose($write);