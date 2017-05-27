<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	die('<center><img src="../img/logo_sparta.png" width="150px"><h4>Acceso incorrecto haga click [<a href="index.php">aqui</a>] para entrar al sistema</h4></center>');
}
$uid = $_SESSION['uid'];
include('../../includes/funciones.php');
include('../../includes/conn.php');
$idcliente   = 0; //en caso de que se vaya a modificar
$rut 	 	 = isset($_POST['rut'])?$_POST['rut']:'';
$rut_serie	 = isset($_POST['rut-serie'])?$_POST['rut-serie']:'';
$nombre 	 = isset($_POST['nombre'])?$_POST['nombre']:'';
$apellido1	 = isset($_POST['apellido1'])?$_POST['apellido1']:'';
$apellido2	 = isset($_POST['apellido2'])?$_POST['apellido2']:'';
$comentarios = isset($_POST['comentarios'])?$_POST['comentarios']:'';
$telefono 	 = isset($_POST['telefono'])?$_POST['telefono']:'';
$movil 		 = isset($_POST['movil'])?$_POST['movil']:'';
$email 	 	 = isset($_POST['email'])?$_POST['email']:'';
$direccion	 = isset($_POST['direccion'])?$_POST['direccion']:'';
$nuevo		 = true;

//-- Verificamos si es nuevo o sera actualizacion
$id = 0;
if($rut && $nombre && !$idcliente) {
	//-- Scripts para crear cliente nuevo
	$sql = "INSERT INTO frm_clientes(nombre, apellido1, apellido2, rut, serie, email, telefono, movil, direccion, comentarios, idusuario)
			SELECT '$nombre', '$apellido1', '$apellido2', '$rut',
				'$rut_serie', '$email', '$telefono', '$movil',
				'$direccion','$comentarios', $uid	
			WHERE NOT EXISTS(SELECT id FROM frm_clientes WHERE rut='$rut')
			RETURNING id";
	$tran = pg_query($conn, $sql);
	if($fila = pg_fetch_assoc($tran)) {
		$id=$fila['id'];
		$str_bien = "Cliente: $nombre se ha guardado correctamente!";
	} else {
		if(@pg_last_error($tran))
			$str_error="Servidor de base de datos ha retornado un error, intente mas tarde";
		else
			$str_error = "Ya existe el Rut que intenta ingresar ($rut)";
	}
}
?>
<script>
<?php
if(isset($str_bien)) {
?>
	$( "#resetFrm" ).trigger( "click" );
	/* Notificacion exitosa */
	new PNotify({
		title  : 'Registro Exitoso!',
		text   : '<?php print $str_bien ?>',
		type   : 'success',
		styling: 'bootstrap3'
	});
<?php
}
if(isset($str_error)) {
?>
	/* Notificacion de error */
	new PNotify({
		title  : 'Error!',
		text   : '<?php print $str_error ?>',
		type   : 'error',
		styling: 'bootstrap3'
	});
<?php
}
?>
	$( "#nombre" ).focus();
	$("html, body").animate({ scrollTop: 0 }, "slow");
</script>
<?php
@pg_close($conn);
?>