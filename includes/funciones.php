<?php
//-- Funcion para generar log de errores
function txt_log($texto) {
    //Write action to txt log
    $path = realpath(dirname(__FILE__));
    $log  = date("F j, Y, g:i a").PHP_EOL.
            "Mensaje: ".$texto.PHP_EOL.
            "-------------------------".PHP_EOL;
    $file = $path.'/sparta_'.date("j.n.Y").'.log';
    if(file_exists($file)) {
        file_put_contents($file, $log, FILE_APPEND);
    } else {
        file_put_contents($file, $log);   
    }
}

//-- Dar formato a fecha
function fec_to_format($fecha, $formato) {
    $dateObj = \DateTime::createFromFormat($formato, $fecha);
    if (!$dateObj) {
        throw new \Exception("Error con el formato para el valor: '$fecha'");
    } 
    $dateUS = $dateObj->format('Y-m-d');
    return $dateUS;
}

//-- Generar registros en log
function glog($idusuario = -1, $nombre = 'root', $modulo='', $accion = '') {
    include('conn.php');
    $texto = "$nombre $modulo: $accion";
    $sql = "INSERT INTO log(idusuario, descripcion)
            VALUES($idusuario, '$texto')";
    $query = pg_query($conn, $sql);
}
?>