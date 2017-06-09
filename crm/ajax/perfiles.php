<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
$username	= $_SESSION['usuario'] ?? '';
$idusuario	= $_SESSION['uid'] ?? 0;
$usr_nombre	= $_SESSION['nombre'] ?? '';
$usr_admin	= $_SESSION['admin'] ?? 0;

$idcargo = $_POST['idcargo'] ?? 0;
$items   = $_POST['items'] ?? '';
$accion  = $_POST['accion'] ?? '';
$perfil  = $_POST['perfil'] ?? '';
if(!$accion || !$idcargo) exit;

include('../../includes/funciones.php');
include('../../includes/conn.php');
if($accion == "buscar") {
    $sql = "SELECT permisos FROM permisos WHERE idcargo = $idcargo";
    $query = pg_query($conn, $sql);
    if($row = pg_fetch_assoc($query)) {
        $cadena = base64_decode($row['permisos']);
        $datos = explode(",", $cadena);
        print "<script>";
        foreach ($datos AS &$valor) {
            //print "$('[value=$valor]').prop('checked', true);";
			if($valor > 0)
				print "$('[value=$valor]').trigger('click');";
        }
        print "</script>";
    }
} elseif($accion == "guardar") {
	$b64 = base64_encode($items);
	$sql = "INSERT INTO permisos (idcargo, permisos) 
				VALUES ($idcargo, '$b64')
			ON CONFLICT (idcargo) DO UPDATE 
			SET permisos = '$b64'";
	if(!$query = pg_query($conn, $sql)) {
		$type 	 = "error";
		$str_sms = "Error inesperado con la base de datos, intente mas tarde o comuniquese con su administrador de sistemas";
		$titulo  = "Error!";
	} else {
		$type 	 = "success";
		$str_sms = "Se han actualizados correctamente los permisos del perfil [$perfil]";
		$titulo  =  "Exito!";
	}
	
}
if(isset($type) && isset($str_sms)) {
?>
<script>
	/* Notificacion */
	new PNotify({
		title  : '<?php print $titulo ?>',
		text   : '<?php print $str_sms ?>',
		type   : '<?php print $type ?>',
		styling: 'bootstrap3'
	});
</script>	
<?php
}
?>