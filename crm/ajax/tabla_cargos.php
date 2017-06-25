<?php
if(!isset($cargos)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	include('../../includes/funciones.php');
	include('../../includes/conn.php');
}
$id_cargo 	 = isset($_POST['id_cargo'])?$_POST['id_cargo']:0;
$descripcion = isset($_POST['descripcion'])?$_POST['descripcion']:'';
$supervisor	 = isset($_POST['supervisor'])?($_POST['supervisor']=='on')?1:0:0;
$id_eliminar = isset($_POST['id-eliminar'])?$_POST['id-eliminar']:0;
$nuevo		 = true;

if($id_eliminar) {
	$sql   = "DELETE FROM cargos WHERE id=$id_eliminar";
	$query = pg_query($conn, $sql);
}

//-- Verificamos si viene $id_etapa para crear nuevo registro o editar
if(!$id_cargo && $descripcion) {
	$sql = "INSERT INTO cargos(descripcion, supervisado)
		    SELECT '$descripcion', $supervisor
			WHERE NOT EXISTS(SELECT id FROM cargos
					         WHERE UPPER(descripcion)=UPPER('$descripcion')
							)
			RETURNING id";

} elseif($id_cargo && $descripcion) {
	$nuevo = false;
	$sql = "UPDATE cargos SET
				descripcion='$descripcion'
				,supervisado = $supervisor
		    WHERE id=$id_cargo
		    RETURNING id";
}
if($descripcion) {
	$query = pg_query($conn, $sql);
	if($row = pg_fetch_assoc($query))
		$id_cargo = $row['id'];
}

//-- Buscar datos para rellenar la tabla
$sql = "SELECT id, descripcion, supervisado
		FROM cargos
		WHERE id>0
		ORDER BY descripcion";
$query= pg_query($conn, $sql);
if(!$nuevo) {
?>
	<input type="hidden" name="id-cargo" id="id-fase" value="<?php print $id_cargo ?>" />
<?php
}
?>
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-fases">
    <thead>
      <tr class="headings">
        <th class="column-title text-left" width="80%">Descripci&oacute;n</th>
        <th class="column-title no-link last text-center" colspan="2"><span class="nobr">Acci&oacute;n</span>
        </th>
      </tr>
    </thead>
    <tbody>
<?php
if(pg_numrows($query) == 0) {
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
	$url   = "&id={$fila['id']}&token=$token&descripcion={$fila['descripcion']}&supervisor={$fila['supervisado']}";
?>
      <tr class="<?php print $clase ?>">
        <td class=" text-left"><?php print $fila['descripcion'] ?></td>
		<td class=" last text-center">
			<a href="?acc=edit&<?php print $url ?>"
			   data-toggle="tooltip"
			   data-placement="bottom"
			   title="Click para Editar <?php print $fila['descripcion'] ?>">
				<i class="fa fa-edit" style="color: green;"></i>
			</a>
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