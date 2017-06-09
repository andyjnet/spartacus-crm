<?php
function comprueba($cadena = "", $permiso = "") {
    $arr = explode(",", $cadena);
    $busca = array_search($permiso, $arr);
    if($busca === false) {
        $retorno = false;
    } else {
        $retorno = true;
    }
    return $retorno;
}
?>