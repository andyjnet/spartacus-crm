<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
$idfile   = isset($_POST['id'])?$_POST['id']:0;
$modulo	  = isset($_POST['modulo'])?$_POST['modulo']:0;
$idmodulo = isset($_POST['idmodulo'])?$_POST['idmodulo']:0;
if(!$idfile) exit;
$username	= $_SESSION['usuario'];
$idusuario	= $_SESSION['uid'];
$usr_nombre	= $_SESSION['nombre'];
$usr_admin	= $_SESSION['admin'];
include('../../includes/funciones.php');
include('../../includes/conn.php');
//-- Buscamos los datos del archivo antes de eliminarlo
$sql = "SELECT ruta FROM adjuntos WHERE id=$idfile AND (idusuario=$idusuario OR $usr_admin=1)";
$query = pg_query($conn, $sql);
if($archivo = pg_fetch_assoc($query))
	$ruta = $archivo['ruta'];

//-- Ahora eliminamos el registro de la base de datos
$sql = "DELETE FROM adjuntos WHERE id = $idfile AND (idusuario=$idusuario OR $usr_admin=1)";
if(!$query = pg_query($conn, $sql)) {
    $str_error = "Ha ocurrido un error con la base de datos, intente mas tarde. Si el problema persiste comuniquese con su administrador";
} else {
    $str_bien = "Archivo eliminado correctamente!";
	//-- Borramos el archivo del servidor
    if(isset($ruta) && file_exists($ruta)) $resp = unlink($ruta);
	
	//-- Actualizamos la lista de adjuntos del modulo
	$sql = "SELECT a.id AS idfile, a.ruta, a.anterior,
                e.descripcion AS etapa,
                a.size_text AS size,
                to_char(a.fecha_reg, 'DD/MM/YYYY HH24:MI:SS') AS fecha_reg
            FROM adjuntos a
                LEFT JOIN etapas_venta e ON(a.idinterno = e.id)
            WHERE modulo = '$modulo'
                AND idmodulo = $idmodulo
            ORDER BY e.orden";
    $query = pg_query($conn, $sql);
?>
    <div class="table-responsive">
        <table class="table table-striped jambo_table bulk_action" id="tbl-adjuntos">
            <thead>
                <tr class="headings">
                    <th class="column-title text-left">Archivo  </th>
                    <th class="column-title text-left">Etapa </th>
                    <th class="column-title text-left">Tamaño </th>
                    <th class="column-title text-left">Fecha </th>
                    <th class="column-title no-link last text-center">
                        <span class="nobr">Acci&oacute;n</span>
                    </th>
                </tr>
            </thead>
            <tbody>
<?php
if(pg_num_rows($query) == 0) {
?>
	  <tr class="even pointer">
		<td class="text-center" colspan="5">No hay documentos adjuntos</td>
	  </tr>
<?php
}
    $act = 1;
    while($fila = pg_fetch_assoc($query)) {
        $clase = (($act%2)==0)?"odd pointer":"even pointer";
?>
      <tr class="<?php print $clase ?>">
        <td class=" text-left"><?php print $fila['anterior'] ?></td>
        <td class=" text-left"><strong><?php print $fila['etapa'] ?></strong></td>
		<td class=" text-left"><?php print $fila['size'] ?></td>
		<td class=" text-left"><?php print $fila['fecha_reg'] ?></td>
		<td class=" last text-center">
            <a href="javascript:void(0)"
			   data-toggle="tooltip" data-placement="bottom"
			   title="Descargar <?php print $fila['anterior'] ?>"
			   onclick="downloadFile(<?php print $fila['idfile']?>);">
				<i class="fa fa-download"></i>
			</a>        
			&nbsp;&nbsp;
            <a href="javascript:void(0)"
			   data-toggle="tooltip" data-placement="bottom"
			   title="Click para eliminar Documento: <?php print $fila['anterior'] ?>"
			   onclick="deleteFile(<?php print $fila['idfile'] ?>);">
				<i class="fa fa-remove" style="color: red;"></i>
			</a>			
        </td>
      </tr>
<?php
        $act += 1;
    }
?>
            </tbody>
        </table>
    </div>        
<?php
}
?>
<script>
	/* limpiamos y cerramos el panel de formulario si todo va bien */
<?php
if(isset($str_bien)) {
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