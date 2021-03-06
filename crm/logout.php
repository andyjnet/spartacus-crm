<?php
session_start();
if(isset($_SESSION['usuario'])) {
    include('../includes/funciones.php');
    glog($_SESSION['uid'], $_SESSION['nombre'], 'acceso', 'logout del sistema');
}
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header("location: ../index.php");
?>