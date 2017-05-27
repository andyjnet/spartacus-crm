<?php
if(!isset($clientes)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	$idusuario = $_SESSION['uid'];
	include('../../includes/funciones.php');
	include('../../includes/conn.php');
}
$idcliente	 = isset($_POST['idcliente'])?$_POST['idcliente']:0;
$tipo 	 	 = isset($_POST['tipo'])?$_POST['tipo']:'';
$rut 	 	 = isset($_POST['rut'])?$_POST['rut']:'';
$nombre 	 = isset($_POST['nombre'])?$_POST['nombre']:'';
$fantasia	 = isset($_POST['fantasia'])?$_POST['fantasia']:'';
$comentarios = isset($_POST['comentarios'])?$_POST['comentarios']:'';
$ejecutivo	 = isset($_POST['ejecutivo'])?$_POST['ejecutivo']:0;
$estado		 = isset($_POST['estado'])?$_POST['estado']:0;
$contacto 	 = isset($_POST['contacto'])?$_POST['contacto']:'';
$telefono 	 = isset($_POST['telefono'])?$_POST['telefono']:'';
$movil 		 = isset($_POST['movil'])?$_POST['movil']:'';
$email 	 	 = isset($_POST['email'])?$_POST['email']:'';
$direccion	 = isset($_POST['direccion'])?$_POST['direccion']:'';
$id_eliminar = isset($_POST['id-eliminar'])?$_POST['id-eliminar']:0;
$nuevo		 = true;

if($id_eliminar) {
	$sql   = "DELETE FROM clientes WHERE id=$id_eliminar";
	$query = pg_query($conn, $sql);
}

//-- Iniciamos transaccion
if($rut && $nombre) pg_query($conn, "BEGIN");

//-- Verificamos si es nuevo o sera actualizacion
$id = 0;
if($rut && $nombre && !$idcliente) {
	//-- Scripts para crear cliente nuevo
	$sql = "INSERT INTO clientes(idorigen, idejecutivo, tipo, rut, nombre, nombre_fantasia, comentarios, idestado, idusuario)
			SELECT 0, $ejecutivo, '$tipo', '$rut', '$nombre', '$fantasia','$comentarios', $estado, $idusuario
			WHERE NOT EXISTS(SELECT id FROM clientes WHERE rut='$rut')
			RETURNING id";
	$tran = pg_query($conn, $sql);
	if($fila = pg_fetch_assoc($tran)) {
		$id=$fila['id'];
		$contacto = ($tipo == 'J')?$contacto:$nombre;
		if($telefono || $movil || $email || $direccion) {
			$sql = "INSERT INTO clientes_contactos(idcliente, nombre, telefono, movil, email, direccion, idusuario)
					VALUES($id, '$contacto', '$telefono', '$movil', '$email', '$direccion', $idusuario)";
			if(!$tran = pg_query($conn, $sql)) {
				$str_error = "el cliente no se ha podido guardar, intente mas tarde";
			}
		}
		if(!isset($str_error))
			$str_bien = "$nombre se ha guardado correctamente!";
	} else {
		if(@pg_last_error($tran)) {
			$str_error="Servidor de base de datos ha retornado un error, intente mas tarde";
		} else {
			$str_error = "Parece que ya existe el Rut que intenta ingresar";
		}
	}
}

//-- Hacemos commit de transaccion
if(isset($str_bien)) {
	pg_query($conn, "COMMIT");
} elseif(isset($str_error)) {
	pg_query($conn, "ROLLBACK");
}

$sql = "SELECT c.id, c.rut, c.nombre,
			cc.telefono, cc.movil, cc.email
		FROM clientes c
			LEFT JOIN clientes_contactos cc ON(cc.idcliente = c.id)
		ORDER BY c.nombre, c.rut";
$query = pg_query($conn, $sql);
?>
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-clientes">
	<thead>
	  <tr class="headings">
		<th class="column-title text-left">Rut  </th>
		<th class="column-title text-left">Nombre </th>
		<th class="column-title text-left">Tel&eacute;fono </th>
		<th class="column-title text-left">Email </th>
		<th class="column-title no-link last text-center"><span class="nobr">Acci&oacute;n</span>
		</th>
	  </tr>
	</thead>
	<tbody>
<?php
$NoActivar = false;
if(pg_num_rows($query) == 0) {
	$NoActivar = true;
?>
	  <tr class="even pointer">
		<td class="text-center" colspan="5">No hay clientes registrados! Haga click en Nuevo para agregar el primero</td>
	  </tr>
<?php
}
$act = 1;
while($fila = pg_fetch_assoc($query)) {
	$clase = (($act%2)==0)?"odd pointer":"even pointer";
	if($fila['telefono'] <> '' && $fila['movil'] <> '') {
		$telefono = $fila['telefono'].' / '.$fila['movil'];
	} elseif($fila['telefono'] <> '') {
		$telefono = $fila['telefono'];
	} elseif($fila['movil'] <> '') {
		$telefono = $fila['movil'];
	} else {
		$telefono = "N/A";
	}
?>
      <tr class="<?php print $clase ?>">
        <td class=" text-left"><strong><?php print  $fila['rut'] ?></strong></td>
        <td class=" text-left"><?php print $fila['nombre'] ?></td>
		<td class=" text-left"><?php print $telefono ?></td>
		<td class=" text-left"><?php print $fila['email'] ?></td>
		<td class=" last text-center">
			<a href="#"
			   data-toggle="tooltip"
			   data-placement="bottom"
			   title="Click para Editar <?php print $fila['nombre'] ?>">
				<i class="fa fa-edit" style="color: green;"></i>
			</a>
			&nbsp;&nbsp;
			<a href="#"
			   data-toggle="tooltip" data-placement="bottom"
			   title="Click para Eliminar <?php print $fila['nombre'] ?>"
			   onclick="fn_elimina(<?php print $fila['id'] ?>,'<?php print $fila['nombre'] ?>');">
				<i class="fa fa-remove" style="color: red;"></i>
			</a>
        </td>
      </tr>
<?php
	$act += 1 ;
}
?>
	</tbody>
  </table>
</div>
<script>
	/* limpiamos y cerramos el panel de formulario si todo va bien */
<?php
if(!isset($clientes) && !$id_eliminar)
	print '/* $("#new-client").click(); */ $( "#resetFrm" ).trigger( "click" );'.PHP_EOL;
if(isset($str_bien)) {
?>
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
</script>
<?php
@pg_close($conn);
?>