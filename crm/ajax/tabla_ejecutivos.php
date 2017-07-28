<?php
if(!isset($ejecutivos)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	$idusuario = $_SESSION['uid'];
	include('../../includes/funciones.php');
	include('../../includes/conn.php');
}
$idejecutivo = isset($_POST['idejecutivo'])?$_POST['idejecutivo']:0;
$rut 	 	 = isset($_POST['rut'])?$_POST['rut']:'';
$ingreso     = isset($_POST['ingreso'])?$_POST['ingreso']:'';
$nombre 	 = isset($_POST['nombre'])?$_POST['nombre']:'';
$apellidos 	 = isset($_POST['apellidos'])?$_POST['apellidos']:'';
$telefono 	 = isset($_POST['telefono'])?$_POST['telefono']:'';
$movil 		 = isset($_POST['movil'])?$_POST['movil']:'';
$email 	 	 = isset($_POST['email'])?$_POST['email']:'';
$direccion	 = isset($_POST['direccion'])?$_POST['direccion']:'';
$cargo 	 	 = isset($_POST['cargo'])?$_POST['cargo']:0;
$nivel	 	 = isset($_POST['nivel'])?$_POST['nivel']:0;
$sucursal 	 = isset($_POST['sucursal'])?$_POST['sucursal']:0;
$comision	 = isset($_POST['comision'])?$_POST['comision']:0.00;
$esUsuario 	 = isset($_POST['usuario'])?$_POST['usuario']:0;
$uname 	 	 = isset($_POST['uname'])?$_POST['uname']:'';
$clave 	 	 = isset($_POST['clave'])?$_POST['clave']:'';
$id_eliminar = isset($_POST['id-eliminar'])?$_POST['id-eliminar']:0;
$pwd		 = isset($_POST['pwd'])?$_POST['pwd']:'';
 $supervisor =  isset($_POST['supervisor'])?$_POST['supervisor']:0;
$nuevo		 = true;
//-- Si viene id a eliminar
if($id_eliminar) {
	$sql   = "DELETE FROM usuarios WHERE id=$id_eliminar";
	$query = pg_query($conn, $sql);
} else {
	//-- Validamos campos (agregar/modificar)
	if(strlen($ingreso) != 10 && $ingreso != "") {
		die("<p class=\"text-center\"><strong>Fecha Incorrecta! ($ingreso)</strong></p>");
	} elseif($ingreso == "") {
		$ingreso= "NOW()";
	} else {
		$ingreso = "'".substr($ingreso, 6, 4).'-'.substr($ingreso, 3, 2).'-'.substr($ingreso,0,2)."'";	
	}
}



//-- Iniciamos transaccion
if($rut && $nombre) pg_query($conn, "BEGIN");

//-- Verificamos si es nuevo o sera actualizacion
$id = 0;
$sw = 1;
if($rut && $nombre && !$idejecutivo) {
	//-- Scripts para crear cliente nuevo
	$sql = "INSERT INTO usuarios(rut, nombre, uname, clave, apellidos, telefono, movil, email, direccion,
								 ingreso, idnivel, idcargo, idsucursal, idusuario, estado, idsupervisor, comision)
			SELECT '$rut', '$nombre', '$uname', md5('$clave'), '$apellidos', '$telefono', '$movil',
				'$email', '$direccion', $ingreso, $nivel, $cargo, $sucursal, $idusuario, $esUsuario, $supervisor, $comision
			WHERE NOT EXISTS(SELECT id FROM usuarios WHERE rut='$rut')
			RETURNING id";
	$texto  = "creado";
	$texto2 = "crear";

} elseif($rut && $nombre && $idejecutivo) {
	if($clave == "" && $pwd) $clave = $pwd; else $clave = md5($clave);
	$sql = "UPDATE usuarios SET
				rut 	   = '$rut'
				,nombre    = '$nombre'
				,uname     = '$uname'
				,clave     = '$clave'
				,apellidos = '$apellidos'
				,telefono  = '$telefono'
				,movil	   = '$movil'
				,direccion = '$direccion'
				,ingreso   = $ingreso
				,idnivel   = $nivel
				,idcargo   = $cargo
				,idsucursal= $sucursal
				,idusuario = $idusuario
				,estado	   = $esUsuario
				,idsupervisor = $supervisor
				,comision  = $comision
			WHERE id = $idejecutivo
			RETURNING id";
	$texto  = "actualizado";
	$texto2 = "actualizar";
} else {
	$sw = 0;
}
if($sw) {
	$tran = pg_query($conn, $sql);
	if($fila = pg_fetch_assoc($tran)) {
		$id=$fila['id'];
		$str_bien = "$nombre se ha $texto correctamente!";
	} else {
		if(@pg_last_error($tran)) {
			$str_error="Servidor de base de datos ha retornado un error, intente mas tarde";
		} else {
			$str_error = "Parece que ya existe el Rut que intenta $texto2";
		}
	}
}

//-- Hacemos commit de transaccion
if(isset($str_bien)) {
	pg_query($conn, "COMMIT");
} elseif(isset($str_error)) {
	pg_query($conn, "ROLLBACK");
}

$sql = "SELECT u.id, u.rut,
			CONCAT(u.nombre, ' ', u.apellidos) AS nombre,
			u.telefono, u.movil, u.email,
			c.descripcion AS cargo,
			s.descripcion AS sucursal
		FROM usuarios u
			LEFT JOIN cargos c ON(u.idcargo=c.id)
			LEFT JOIN sucursales s ON(u.idsucursal = s.id)
		WHERE u.id>0
		ORDER BY u.nombre, u.apellidos";
$query = pg_query($conn, $sql);
?>
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-ejecutivos">
	<thead>
	  <tr class="headings">
		<th class="column-title text-left">Rut  </th>
		<th class="column-title text-left">Nombre </th>
		<th class="column-title text-left">Tel&eacute;fono </th>
		<th class="column-title text-left">Email </th>
		<th class="column-title text-left">Cargo </th>
		<th class="column-title text-left">Sucursal </th>
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
		<td class="text-center" colspan="7">No hay ejecutivos registrados! Haga click en Nuevo para agregar el primero</td>
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
		<td class=" text-left"><?php print $fila['cargo'] ?></td>
		<td class=" text-left"><?php print $fila['sucursal'] ?></td>
		<td class=" last text-center">
			<a href="#"
			   data-toggle="tooltip"
			   data-placement="bottom"
			   title="Click para Editar <?php print $fila['nombre'] ?>"
			   onclick="fn_modifica(<?php print $fila['id'] ?>);"
			   id="a-editar<?php print $fila['id'] ?>">
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
if(!isset($ejecutivos) && !$id_eliminar)
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