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
?>