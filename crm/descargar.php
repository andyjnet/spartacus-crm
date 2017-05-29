<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
$idfile = isset($_GET['idfile'])?$_GET['idfile']:0;
if(!$idfile) exit;
$username	= $_SESSION['usuario'];
$idusuario	= $_SESSION['uid'];
$usr_nombre	= $_SESSION['nombre'];
$usr_admin	= $_SESSION['admin'];
include('../includes/funciones.php');
include('../includes/conn.php');
$sql = "SELECT ruta, anterior, tipo
        FROM adjuntos
        WHERE id=$idfile";
$query = pg_query($conn, $sql);
if($file = pg_fetch_assoc($query)) {
    if(!file_exists("ajax/".$file['ruta']))
        exit;
    $archivo = "ajax/".$file['ruta'];
    header('Content-Description: File Transfer');
    header('Content-Type: '.$file['tipo']);
    header('Content-Disposition: attachment; filename="'.$file['anterior'].'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($archivo));
    ob_clean();
    flush();
    readfile($archivo);
    exit;    
}
?>