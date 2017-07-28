<?php
if(!isset($campaigns)) {
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	include('../../includes/funciones.php');
	include('../../includes/conn.php');
}
if(file_exists('../../includes/tools.php'))
	include_once('../../includes/tools.php');
if(file_exists('../includes/tools.php'))
	include_once('../includes/tools.php');

$id_campaign = isset($_POST['id_campaign'])?$_POST['id_campaign']:0;
$descripcion = isset($_POST['descripcion'])?$_POST['descripcion']:'';
$margen		 = isset($_POST['margen'])?$_POST['margen']:0.00;
$inicio		 = isset($_POST['inicio'])?$_POST['inicio']:'01/01/2017';
$fin		 = isset($_POST['fin'])?$_POST['fin']:'01/01/2017';
$retorno	 = isset($_POST['retorno'])?$_POST['retorno']:0;
$comision	 = isset($_POST['comision'])?$_POST['comision']:0.00;
$id_eliminar = isset($_POST['id-eliminar'])?$_POST['id-eliminar']:0;
$nuevo		 = true;

if($id_eliminar) {
	$sql   = "DELETE FROM campaign WHERE id = $id_eliminar";
	$query = pg_query($conn, $sql);
} else {
	$comision = is_numeric($comision)?$comision:0.00;
	$margen = is_numeric($margen)?$margen:0.00;
	//-- Campos de Fecha
	try  {
		$inicio = fec_to_format($inicio, "d/m/Y");
	} catch(Exception $e) {
	  $inicio = "2017-01-01";
	}
	try  {
		$fin = fec_to_format($fin, "d/m/Y");
	} catch(Exception $e) {
	  $fin = "2017-01-01";
	}
	if($inicio == $fin) {
		$inicio = "null";
		$fin = "null";
	} else {
		$inicio = "'$inicio'::date";
		$fin = "'$fin'::date";
	}
}

//-- Verificamos si viene $id_ramo para crear nuevo registro o editar
if(!$id_campaign && $descripcion) {
	$sql = "INSERT INTO campaign(descripcion, margen, comision, inicio, fin, id_retorno)
		    SELECT '$descripcion', $margen, $comision, $inicio, $fin, $retorno
			WHERE NOT EXISTS(SELECT id FROM campaign
					         WHERE UPPER(descripcion)=UPPER('$descripcion')
								AND inicio::date = $inicio
								AND fin::date = $fin
							)
			RETURNING id";

} elseif($id_campaign && $descripcion) {
	$nuevo = false;
	$sql="UPDATE campaign SET
			descripcion='$descripcion'
			,margen = $margen
			,comision = $comision
			,inicio = $inicio
			,fin = $fin
			,id_retorno = $retorno
		  WHERE id=$id_campaign
		  RETURNING id";
}
if($descripcion) {
	$query = pg_query($conn, $sql);
	if($row = pg_fetch_assoc($query))
		$id_ramo = $row['id'];
}

//-- Buscar datos para rellenar la tabla
$sql = "SELECT id, descripcion, estado,
			margen, comision,
			TO_CHAR(inicio,'DD/MM/YYYY') AS inicio,
			TO_CHAR(fin,'DD/MM/YYYY') AS fin,
			id_retorno
		FROM campaign
		WHERE id > 0
		ORDER BY descripcion";
$query= pg_query($conn, $sql);
if(!$nuevo) {
?>
	<input type="hidden" name="id-campaign" id="id-campaign" value="<?php print $id_campaign ?>" />
<?php
}
?>
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-campaign">
    <thead>
      <tr class="headings">
        <th class="column-title text-left">
			Descripci&oacute;n
		</th>
        <th class="column-title no-link last text-center" colspan="2">
			<span class="nobr">Acci&oacute;n</span>
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
$script = "";
while($fila = pg_fetch_assoc($query)) {
	$clase = (($act%2)==0)?"odd pointer":"even pointer";
	$token = md5($fila['id'].$fila['descripcion'].$fila['margen'].$fila['comision'].$fila['inicio'].$fila['fin'].$fila['id_retorno'].'ajbc');
	$url   = "id={$fila['id']}&token=$token&descripcion={$fila['descripcion']}&margen={$fila['margen']}&comision={$fila['comision']}&";
	$url  .= "inicio={$fila['inicio']}&fin={$fila['fin']}&retorno={$fila['id_retorno']}";
	$sel   = '';
	if($fila['estado'] == 1)
		$sel = " selected";
	$script .= '<option value="'.$fila['id'].'"'.$sel.'>'.$fila['descripcion'].'</option>'
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
print "<script>$('#activa').html('$script');</script>";
?>
    </tbody>
  </table>
</div>
<?php
pg_close($conn);
?>