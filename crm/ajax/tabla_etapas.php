<?php
if(!isset($etapas)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	include('../../includes/funciones.php');
	include('../../includes/conn.php');
}
$id_etapa 	 = isset($_POST['id_etapa'])?$_POST['id_etapa']:0;
$descripcion = isset($_POST['descripcion'])?$_POST['descripcion']:'';
$orden		 = isset($_POST['orden'])?$_POST['orden']:1;
$adjunto	 = isset($_POST['adjunto'])?($_POST['adjunto']=='on')?'true':'false':'false';
$masivo		 = isset($_POST['masivo'])?($_POST['masivo']=='on')?'true':'false':'false';
$estado		 = isset($_POST['estado'])?($_POST['estado']=='on')?1:0:0;
$id_eliminar = isset($_POST['id-eliminar'])?$_POST['id-eliminar']:0;
$nuevo		 = true;

if($id_eliminar) {
	$sql   = "DELETE FROM etapas_venta WHERE id=$id_eliminar";
	$query = pg_query($conn, $sql);
}

//-- Verificamos si viene $id_etapa para crear nuevo registro o editar
if(!$id_etapa && $descripcion) {
	$sql = "INSERT INTO etapas_venta(descripcion, orden, masivo, estado, adjunto)
		    SELECT '$descripcion', $orden, $masivo, $estado, $adjunto
			WHERE NOT EXISTS(SELECT id FROM etapas_venta
					         WHERE UPPER(descripcion)=UPPER('$descripcion')
							)
			RETURNING id";

} elseif($id_etapa && $descripcion) {
	$nuevo = false;
	$sql="UPDATE etapas_venta SET
			descripcion='$descripcion',
			orden=$orden,
			adjunto=$adjunto,
			masivo=$masivo,
			estado=$estado
		  WHERE id=$id_etapa
		  RETURNING id";
}
if($descripcion) {
	$query = pg_query($conn, $sql);
	if($row = pg_fetch_assoc($query))
		$id_etapa = $row['id'];
}

//-- Buscar datos para rellenar la tabla
$sql = "SELECT id, descripcion, orden, adjunto, masivo, estado
		FROM etapas_venta
		ORDER BY orden";
$query= pg_query($conn, $sql);
if(!$nuevo) {
?>
	<input type="hidden" name="id-etapa" id="id-etapa" value="<?php print $id_etapa ?>" />
<?php
}
?>
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-etapas">
    <thead>
      <tr class="headings">
        <th class="column-title text-center">Orden  </th>
        <th class="column-title text-left">Descripci&oacute;n </th>
        <th class="column-title no-link last text-center" colspan="2"><span class="nobr">Acci&oacute;n</span>
        </th>
      </tr>
    </thead>
    <tbody>
<?php
if(pg_num_rows($query) == 0) {
?>
      <tr class="even pointer">
        <td class=" text-center" colspan="3"><strong>Sin registros para mostrar!</strong></td>
        </td>
      </tr>
<?php
}
$act = 1;
while($fila = pg_fetch_assoc($query)) {
	$clase = (($act%2)==0)?"odd pointer":"even pointer";
	$token = md5($fila['id'].'ajbc');
	$url   = "id={$fila['id']}&token=$token&orden={$fila['orden']}&adjunto={$fila['adjunto']}&masivo={$fila['masivo']}&estado={$fila['estado']}&descripcion={$fila['descripcion']}";
?>
      <tr class="<?php print $clase ?>">
        <td class=" text-center"><strong><?php print  $fila['orden'] ?></strong></td>
        <td class=" text-left"><?php print $fila['descripcion'] ?></td>
		<td class=" last text-center">
			<a href="?acc=edit&<?php print $url ?>"
			   data-toggle="tooltip"
			   data-placement="bottom"
			   title="Click para Editar <?php print $fila['descripcion'] ?>">
				<i class="fa fa-edit" style="color: green;"></i>
			</a>
		</td>
        <td class=" last text-center">
			<a href="#"
			   data-toggle="tooltip" data-placement="bottom"
			   title="Click para Eliminar <?php print $fila['descripcion'] ?>"
			   onclick="fn_elimina(<?php print $fila['id'] ?>,'<?php print $fila['descripcion'] ?>')">
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
<?php
pg_close($conn);
?>