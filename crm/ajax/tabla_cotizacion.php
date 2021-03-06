<?php
if(!isset($cotizaciones)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	$idusuario    = $_SESSION['uid'];
	$usr_admin    = $_SESSION['admin'];
	$usr_nombre   = $_SESSION['nombre'];
	include('../../includes/funciones.php');
	include('../../includes/conn.php');
}
if(file_exists('../../includes/tools.php'))
	include_once('../../includes/tools.php');
if(file_exists('../includes/tools.php'))
	include_once('../includes/tools.php');
	
//-- Verificar permisos sobre etapas
$usr_permisos = $_SESSION['permisos'] ?? '';
//$id_campaign = $_SESSION['campaign'] ?? 0;
$usr_etapas   = '';
$usr_cotizaciones = '';
if($usr_permisos && !$usr_admin) {
	$access = explode(",", $usr_permisos);
	foreach ($access as $item) {
		if(intval($item) > 1000) {
			$usr_etapas .= (intval($item) - 1000).',';
		}
	}
	if($usr_etapas) {
		$usr_etapas = substr($usr_etapas, 0, strlen($usr_etapas) - 1);
		$usr_etapas = " OR p.idetapa IN($usr_etapas) ";
	} else {
		$usr_cotizaciones = " OR p.idejecutivo = $idusuario ";
	}
} else {
	$access = array("");
}

//-- Clase para manipulacion de adjuntos
if(!empty($_FILES) && $_FILES['files']['tmp_name']) {
    include('../../includes/class.fileuploader.php');
    // initialize the FileUploader
    $FileUploader = new FileUploader('files', array(
        // Options will go here
    ));
    // call to upload the files
    $upload = $FileUploader->upload();
	if($upload['isSuccess']) {
		// get the uploaded files
		$files = $upload['files'];
		$file_ruta 			  = $files[0]['file'];
		$file_nombre		  = $files[0]['name'];
		$file_anterior		  = $files[0]['old_name'];
		$file_anterior_titulo = $files[0]['old_title'];
		$file_size_bytes	  = $files[0]['size'];
		$file_size_text		  = $files[0]['size2'];
		$file_titulo		  = $files[0]['title'];
		$file_extension		  = $files[0]['extension'];
		$file_tipo			  = $files[0]['type'];
		
	}
	if($upload['hasWarnings']) {
		// get the warnings
		$warnings = $upload['warnings'];
	};
	
	// manipular error
	if(isset($warnings)) {
		print_r($warnings);
		exit;		
	}	
}
//-- Lista de variables
$idcotizacion 	 = isset($_POST['idcotizacion'])?$_POST['idcotizacion']:0;
$idcliente	   	 = isset($_POST['idcliente'])?$_POST['idcliente']:0;
$rut 	 	  	 = isset($_POST['rut'])?$_POST['rut']:'';
$cliente 	  	 = isset($_POST['cliente'])?$_POST['cliente']:'';
$fantasia	  	 = isset($_POST['nom-fantasia'])?$_POST['nom-fantasia']:'';
$contacto  	  	 = isset($_POST['nom-contacto'])?$_POST['nom-contacto']:'';
$telefono 	  	 = isset($_POST['telefono'])?$_POST['telefono']:'';
$movil 		  	 = isset($_POST['movil'])?$_POST['movil']:'';
$email 	 	  	 = isset($_POST['email'])?$_POST['email']:'';
$direccion		 = isset($_POST['direccion'])?$_POST['direccion']:'';
$region			 = isset($_POST['region'])?$_POST['region']:'';
$provincia		 = isset($_POST['provincia'])?$_POST['provincia']:'';
$comuna			 = isset($_POST['comuna'])?$_POST['comuna']:'';
$etapa	  	  	 = isset($_POST['etapa'])?$_POST['etapa']:0;
$etapa_actual	 = isset($_POST['etapa-actual'])?$_POST['etapa-actual']:0;
$sucursal	  	 = isset($_POST['sucursal'])?$_POST['sucursal']:0;
$corredor 	  	 = isset($_POST['corredor'])?$_POST['corredor']:0;
$producto		 = isset($_POST['producto'])?$_POST['producto']:0;
$ejecutivo	  	 = isset($_POST['ejecutivo'])?$_POST['ejecutivo']:0;
$ramo		  	 = isset($_POST['ramo'])?$_POST['ramo']:0;
$patente	  	 = isset($_POST['patente'])?$_POST['patente']:'';
$marca	  	  	 = isset($_POST['marca'])?$_POST['marca']:'';
$modelo	  	  	 = isset($_POST['modelo'])?$_POST['modelo']:'';
$year		  	 = isset($_POST['year'])?$_POST['year']:'';
$chasis		  	 = isset($_POST['chasis'])?$_POST['chasis']:'';
$motor		  	 = isset($_POST['motor'])?$_POST['motor']:'';
$condominio		 = isset($_POST['condominio'])?$_POST['condominio']:'';
$siniestros	  	 = isset($_POST['siniestros'])?$_POST['siniestros']:0.00;
$prima	  	  	 = isset($_POST['prima'])?$_POST['prima']:0.00;
$prima_neta	  	 = isset($_POST['prima-neta'])?$_POST['prima-neta']:0.00;
$cuota			 = isset($_POST['cuota'])?$_POST['cuota']:0.00;
$monto_asegurado = isset($_POST['monto-asegurado'])?$_POST['monto-asegurado']:0.00;
$comision 		 = isset($_POST['comision'])?$_POST['comision']:0.00;
$vigencia		 = isset($_POST['vigencia'])?$_POST['vigencia']:'01/01/2017';
$renovacion		 = isset($_POST['renovacion'])?$_POST['renovacion']:'01/01/2017';
$comentarios	 = isset($_POST['comentarios'])?$_POST['comentarios']:'';
$poliza	 		 = isset($_POST['poliza'])?$_POST['poliza']:'';
$forma_pago		 = isset($_POST['forma-pago'])?$_POST['forma-pago']:0;
$deducible		 = isset($_POST['deducible'])?$_POST['deducible']:'';
$idcompania		 = isset($_POST['cia'])?$_POST['cia']:0;
$id_campaign	 = isset($_POST['campaign'])?$_POST['campaign']:0;

$comision_usuario = 0;
if($ejecutivo) {
   $sql = "SELECT comision FROM usuarios WHERE id=$ejecutivo";
   $query = pg_query($conn, $sql);
   $row = pg_fetch_assoc($query);
   $comision_usuario = $row['comision'];
}

$id_eliminar 	 = isset($_POST['id-eliminar'])?$_POST['id-eliminar']:0;
$nuevo		 = true;
//-- concatenados
$ramo = strpos($ramo, ':')?explode(':', $ramo)[0]:0;

//-- Campos numericos
$siniestros 	 = is_numeric($siniestros)?$siniestros:0.00;
$prima			 = is_numeric($prima)?$prima:0.00;
$prima_neta 	 = is_numeric($prima_neta)?$prima_neta:0.00;
$cuota			 = is_numeric($cuota)?$cuota:0.00;
$monto_asegurado = is_numeric($monto_asegurado)?$monto_asegurado:0.00;
$comision 		 = is_numeric($comision)?$comision:0.00;

//-- Campos de Fecha
try  {
	$vigencia = fec_to_format($vigencia, "d/m/Y");
} catch(Exception $e) {
  $vigencia = "2017-01-01";
}
try  {
	$renovacion= fec_to_format($renovacion, "d/m/Y");
} catch(Exception $e) {
  $renovacion = "2017-01-01";
}

//-- En caso de eliminacion de registro
if($id_eliminar) {
	$eliminado = true;
	$sql = "SELECT c.nombre, p.idetapa
			FROM cotizacion p
				INNER JOIN clientes c ON(p.idcliente=c.id)
			WHERE p.id=$id_eliminar";
	$query = pg_query($conn, $sql);
	if ($row = pg_fetch_assoc($query)) {
		$cliente_e = $row['nombre'];
		$etapa_e = $row['idetapa'];
	} else {
		$cliente_e = "";
		$etapa_e = 0;
	}
	$sql   = "DELETE FROM cotizacion WHERE id=$id_eliminar";
	if(!$query = pg_query($conn, $sql)) {
		$eliminado = false;
	} else {
		//-- Log de acciones
		glog($idusuario, $usr_nombre, 'cotizacion',"Registro Eliminado ID [$id_eliminar] Cliente [$cliente_e] Etapa [$etapa_e]");		
		
	}
}

//-- Iniciamos transaccion
if($rut && $cliente) pg_query($conn, "BEGIN");

//-- Verificamos si es nuevo o sera actualizacion
$id = 0;
$sw = 1;
if($rut && !$idcotizacion) {
	//-- Scripts para crear nueva Cotizacion
	$sql = "INSERT INTO cotizacion(idcliente,idsucursal,idcorredor,idramo,idejecutivo,
				siniestros,prima_actual,vigencia,renovacion, prima_neta, cuota,
				monto_asegurado,comision_corredor,observacion,
				poliza,idetapa,idusuario, idproducto,
				deducible, idforma_pago, idcompania, idcampaign)
			VALUES ($idcliente,$sucursal,$corredor,$ramo,$ejecutivo,
				$siniestros,$prima,'$vigencia'::text::date,'$renovacion'::text::date,$prima_neta, $cuota,
				$monto_asegurado, $comision, '$comentarios'::text,
				'$poliza',$etapa, $idusuario, $producto,
				'$deducible', $forma_pago, $idcompania, $id_campaign)
			RETURNING id;";
	$texto  = "creado";
	$texto2 = "crear";
	
} elseif($rut && $idcotizacion) {
	$sql = "UPDATE cotizacion SET
				idcliente = $idcliente
				,idsucursal = $sucursal
				,idcorredor = $corredor
				,idramo = $ramo
				,idejecutivo = $ejecutivo
				,siniestros = $siniestros
				,prima_actual = $prima
				,vigencia = '$vigencia'::text::date
				,renovacion = '$renovacion'::text::date
				,prima_neta = $prima_neta
				,cuota = $cuota
				,monto_asegurado = $monto_asegurado
				,comision_corredor = $comision
				,comision = $comision_usuario
				,observacion = '$comentarios'::text
				,poliza = '$poliza'
				,idetapa = $etapa
				,idusuario_mod = $idusuario
				,fecha_mod = NOW()
				,idproducto = $producto
				,deducible = '$deducible'
				,idforma_pago = $forma_pago
				,idcompania = $idcompania
				,idcampaign = $id_campaign
			WHERE id = $idcotizacion
			RETURNING id";
	$texto  = "actualizado";
	$texto2 = "actualizar";
	//--- notificacion a ejecutivo
	if($idusuario <> $ejecutivo) {
		$sql2 = "SELECT CONCAT(nombre, ' ', apellidos) AS usuario, email FROM usuarios WHERE id=$ejecutivo AND email<>''";
		$query = pg_query($sql2);
		if($row = pg_fetch_assoc($query)) {
			$correo = $row['email'];
			$usr 	= $row['usuario'];
			$sql2 	= "SELECT CONCAT(nombre, ' ', apellidos) AS usr_activo FROM usuarios WHERE id=$idusuario";
			$query 	= pg_query($sql2);
			if($row = pg_fetch_assoc($query)) {
				$usr_activo = $row['usr_activo'];
				notificar($correo, $usr, $idcotizacion, "Cotización ID: [$idcotizacion] ha sido MODIFICADA por <strong>$usr_activo</strong>, se recomienda verificar cambios");
			}
		}
	}
} else {
	$sw = 0;
}

if($sw) {
	$tran = pg_query($conn, $sql) or die("Error con Base de datos<br />$sql");
	if($fila = pg_fetch_assoc($tran)) {
		$idcotizacion = $fila['id'];
		if($idcliente) {
			$sql  = "DELETE FROM clientes_contactos WHERE idcliente = $idcliente";
			$tran = pg_query($conn, $sql);
			if($telefono || $movil || $email || $contacto || $direccion || $region || $provincia || $comuna) {
				$sql = "INSERT INTO clientes_contactos(idcliente, nombre, telefono, movil, email, idusuario,
							direccion, region, provincia, comuna)
						VALUES($idcliente, '$contacto', '$telefono', '$movil', '$email', $idusuario,
							UPPER('$direccion'), UPPER('$region'), UPPER('$provincia'), UPPER('$comuna'))";
				if(!$tran = pg_query($conn, $sql)) {
					$str_error = "el cliente no se ha podido actualizar, intente mas tarde";
				}
			}

		}
		$sql = "DELETE FROM cotizacion_vehiculos WHERE idcotizacion = $idcotizacion";
		$tran = pg_query($conn, $sql);
		if($patente || $marca || $modelo || $year || $chasis || $motor) {
			$sql = "INSERT INTO cotizacion_vehiculos(idcotizacion, patente, marca, modelo, year, chasis, motor)
					VALUES($idcotizacion, '$patente', '$marca', '$modelo', '$year', '$chasis', '$motor')";
			if(!$tran = pg_query($conn, $sql)) {
				$str_error = "La cotizaci&oacute;n no se ha podido actualizar, intente mas tarde";
			}			
		}
		$sql = "DELETE FROM cotizacion_condominios WHERE idcotizacion = $idcotizacion";
		$tran = pg_query($conn, $sql);
		if($condominio) {
			$sql = "INSERT INTO cotizacion_condominios(idcotizacion, codigo)
					VALUES($idcotizacion, '$condominio')";
			if(!$tran = pg_query($conn, $sql)) {
				$str_error = "La cotizaci&oacute;n no se ha podido actualizar (condominio), intente mas tarde";
			}			
		}
		//-- bof Manipulacion de adjuntos
		if(isset($files)) {
			$sql = "INSERT INTO adjuntos(modulo, idmodulo, idinterno, ruta, nombre,
						anterior, anterior_titulo, size_bytes, size_text,
						titulo, extension, tipo, idusuario)
					VALUES('cotizacion', $idcotizacion, $etapa, '$file_ruta', '$file_nombre',
						'$file_anterior', '$file_anterior_titulo', '$file_size_bytes', '$file_size_text',
						'$file_titulo', '$file_extension', '$file_tipo', $idusuario)
					RETURNING id";
			if(!$tran = pg_query($conn, $sql))
				$str_error = "Ha ocurrido un problema agregando el archivo adjunto";
		}		
		//-- eof Manipulacion de adjuntos

		//-- bof Notificacion por correo
		if($idusuario <> $ejecutivo && $etapa <> $etapa_actual) {
			$sql2 	= "SELECT CONCAT(nombre, ' ', apellidos) AS usr_activo FROM usuarios WHERE id=$idusuario";
			$query 	= pg_query($sql2);
			$row = pg_fetch_assoc($query);
			$usr_activo = $row['usr_activo'];
			$sql2 = "SELECT u.id, CONCAT(nombre, ' ', apellidos) AS nombre,
						p.permisos, u.email
					 FROM usuarios u
						INNER JOIN permisos p ON(u.idcargo = p.idcargo)
					 WHERE u.id <> $idusuario
						AND u.email <> ''";
			$query = pg_query($sql2);
			while($row = pg_fetch_assoc($query)) {
				$permiso = $row['permisos'];
				$permiso = base64_decode($permiso);
				$access = explode(",", $permiso);
				foreach ($access as $item) {
				  $tmp = intval($item);
				  if($tmp > 1000) {
					$etapa_permiso = $tmp - 1000;
					if($etapa == $etapa_permiso) {
						notificar($row['email'], $row['nombre'], $idcotizacion, "Cotización ID: [$idcotizacion] ha sido cambiada de estado por <strong>$usr_activo</strong>, se recomienda ingresar al sistema y revisar a la brevedad posible.");
					}
				  }
				}
			}
		}
		//-- eof Notificacion por correo

		//-- Log de acciones
		glog($idusuario, $usr_nombre, 'cotizacion',"Registro $texto ID [$idcotizacion] etapa [$etapa] Prima [$prima] Prima Neta [$prima_neta] Comision Corredor [$comision]");		
		if(!isset($str_error))
			$str_bien = "La cotizaci&oacute;n se ha $texto correctamente!";
	} else {
		if(@pg_last_error($tran)) {
			$str_error="Servidor de base de datos ha retornado un error, intente mas tarde";
		} else {
			$str_error = "Parece que ya existe un registro de cotizaci&oacute;n similar";
		}
	}
}

//-- Hacemos commit de transaccion
if(isset($str_bien)) {
	pg_query($conn, "COMMIT");
} elseif(isset($str_error)) {
	pg_query($conn, "ROLLBACK");
}
//-- Log de Adjunto
if(isset($files) && isset($file_anterior)) {
	glog($idusuario, $usr_nombre, 'adjuntos', "Archivo $file_anterior / $file_nombre adjuntado a cotizacion [$idcotizacion] en etapa [$etapa]");
}
?>
<script>
	/* limpiamos y cerramos el panel de formulario si todo va bien */
<?php
if(!isset($cotizaciones) && !$id_eliminar)
	print '$( "#resetFrm" ).trigger( "click" );'.PHP_EOL;
if(isset($str_bien) || ($id_eliminar && $eliminado)) {
	$str_bien = $id_eliminar?"El registro se ha eliminado correctamente":$str_bien;
?>
	/* Notificacion exitosa */
	new PNotify({
		title  : 'Exito!',
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