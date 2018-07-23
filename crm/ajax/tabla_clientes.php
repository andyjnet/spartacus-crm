<?php
if(!isset($clientes)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	$idusuario    = $_SESSION['uid'];
	$usr_nombre   = $_SESSION['nombre'];
	$usr_admin	  = $_SESSION['admin'] ?? 0;
	$usr_permisos = $_SESSION['permisos'] ?? '';
}
$arch = '../../includes/funciones.php';
if(file_exists($arch)) include_once($arch);
$arch = '../../includes/conn.php';
if(file_exists($arch)) include_once($arch);
$arch = '../../includes/tools.php';
if(file_exists($arch)) include_once($arch);

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
	$sql = "SELECT nombre, rut, idejecutivo FROM clientes WHERE id=$id_eliminar";
	$query = pg_query($conn, $sql);
	if($row = pg_fetch_assoc($query)) {
		$nombre_e    = $row['nombre'];
		$rut_e 	     = $row['rut'];
		$ejecutivo_e = $row['idejecutivo'];
	} else {
		$nombre_e    = '';
		$rut_e 	     = '';
		$ejecutivo_e = '';		
	}
	$sql   = "DELETE FROM clientes WHERE id=$id_eliminar";
	$query = pg_query($conn, $sql);
	//-- Log de acciones
	glog($idusuario, $usr_nombre, 'cliente',"Registro Eliminado ID [$id_eliminar] Nombre [$nombre_e] RUT [$rut_e] Ejecutivo [$ejecutivo_e]");					
	
}

//-- Iniciamos transaccion
if($rut && $nombre) pg_query($conn, "BEGIN");

//-- Verificamos si es nuevo o sera actualizacion
$id = 0;
$sw = 1;
if($rut && $nombre && !$idcliente) {
	//-- Scripts para crear cliente nuevo
	$sql = "INSERT INTO clientes(idorigen, idejecutivo, tipo, rut, nombre, nombre_fantasia, comentarios, idestado, idusuario)
			SELECT 0, $ejecutivo, '$tipo', '$rut', '$nombre', '$fantasia','$comentarios', $estado, $idusuario
			WHERE NOT EXISTS(SELECT id FROM clientes WHERE REPLACE(rut,'.','') = REPLACE('$rut','.',''))
			RETURNING id";
	$texto  = "creado";
	$texto2 = "crear";
	
} elseif($rut && $nombre && $idcliente) {
	$sql = "UPDATE clientes SET
				idejecutivo = $ejecutivo
				,tipo = '$tipo'
				,rut = '$rut'
				,nombre = '$nombre'
				,nombre_fantasia = '$fantasia'
				,comentarios = '$comentarios'
				,idestado = $estado
				,idusuario_mod = $idusuario
				,fecha_mod = NOW()
			WHERE id = $idcliente
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
		$contacto = ($tipo == 'J')?$contacto:$nombre;
		if($idcliente) {
			$sql  = "DELETE FROM clientes_contactos WHERE idcliente = $idcliente";
			$tran = pg_query($conn, $sql);
		}
		if($telefono || $movil || $email || $direccion || $contacto) {
			$sql = "INSERT INTO clientes_contactos(idcliente, nombre, telefono, movil, email, direccion, idusuario)
					VALUES($id, '$contacto', '$telefono', '$movil', '$email', '$direccion', $idusuario)";
			if(!$tran = pg_query($conn, $sql)) {
				$str_error = "el cliente no se ha podido guardar, intente mas tarde";
			}
		}
		if(!isset($str_error)) {
			//-- Log de acciones
			glog($idusuario, $usr_nombre, 'cliente',"Registro $texto ID [$id] Nombre [$nombre] RUT [$rut] Ejecutivo [$ejecutivo]");					
			$str_bien = "$nombre se ha $texto correctamente!";
		}
	} else {
		if(@pg_last_error($tran)) {
			$str_error="Servidor de base de datos ha retornado un error, intente mas tarde";
		} else {
			// --> bof proceso de reactivacion de cliente
			$sql = "SELECT id FROM clientes WHERE REPLACE(rut,'.','') = REPLACE('$rut','.','') AND idestado = -1";
			$query = pg_query($conn, $sql);
			if($fila = pg_fetch_assoc($query)) {
			  // --> Caso particular, si el cliente existe pero estaba oculto
			  $id = $fila['id'];
			  $sql = "UPDATE clientes SET idestado = 1, fecha_mod=CURRENT_DATE, idusuario_mod = $idusuario WHERE id=$id";
			  if(!$query = pg_query($conn, $sql)) {
				$str_error = "No se pudo reactivar el cliente, favor notifique a departamento TI";
				glog($idusuario, $usr_nombre, 'cliente', "Error intentando reactivar cliente ID [$id] Nombre [$nombre] RUT [$rut] Ejecutivo [$ejecutivo]");
			  } else {
				$str_bien = "$nombre se ha Reactivado correctamente!";
			  }
	        // <-- eof (si se descarta este bloque hay que tomar en cuenta las siguientes llaves del if)
			} else {
			  // --> Procedimiento normal, creando cliente que ya existe
			  $str_error = "Parece que ya existe el Rut que intenta $texto2";
			}
		}
	}
}


//-- Hacemos commit de transaccion
if(isset($str_bien)) {
	pg_query($conn, "COMMIT");
} elseif(isset($str_error)) {
	pg_query($conn, "ROLLBACK");
}

//-- Filtro de clientes segun perfil
$filtro = '';
if(!$usr_admin && !comprueba($usr_permisos, "2")) {
	$filtro = " AND (c.idejecutivo = $idusuario OR c.idusuario = $idusuario) ";
} 
$sql = "SELECT c.id, REPLACE(c.rut,'.','') AS rut, c.nombre,
			cc.telefono, cc.movil, cc.email
		FROM clientes c
			LEFT JOIN clientes_contactos cc ON(cc.idcliente = c.id)
		WHERE c.idestado > -1
		$filtro
		ORDER BY c.nombre, rut";
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
<?php
if($usr_admin == 1 || comprueba($usr_permisos, "4")) {
?>
			<a href="#"
			   data-toggle="tooltip"
			   data-placement="bottom"
			   title="Click para Editar <?php print $fila['nombre'] ?>"
			   onclick="fn_modifica(<?php print $fila['id'] ?>);"
			   id="a-editar<?php print $fila['id'] ?>">
				<i class="fa fa-edit" style="color: green;"></i>
			</a>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "5")) {
?>
			&nbsp;&nbsp;
			<a href="#"
			   data-toggle="tooltip" data-placement="bottom"
			   title="Click para Eliminar <?php print $fila['nombre'] ?>"
			   onclick="fn_elimina(<?php print $fila['id'] ?>,'<?php print $fila['nombre'] ?>');">
				<i class="fa fa-remove" style="color: red;"></i>
			</a>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "8")) {
?>
			&nbsp;&nbsp;
			<a href="cotizacion.php?cid=<?php print base64_encode($fila['id']) ?>"
			   data-toggle="tooltip" data-placement="bottom"
			   title="Nueva Cotizaci&oacute;n para <?php print $fila['nombre'] ?>">
				<i class="fa fa-calculator"></i>
			</a>
<?php
}
?>
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