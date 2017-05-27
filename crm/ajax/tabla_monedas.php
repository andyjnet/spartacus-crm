<?php
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
if(!isset($_POST['id_moneda']) || !isset($_POST['id_valor'])) {
    die("<p class=\"text-center\"><strong>Parametros incorrectos!</strong></p>");
}
$id_moneda = $_POST['id_moneda'];
$id_valor  = $_POST['id_valor'];
if(!is_numeric($id_moneda) || !is_numeric($id_valor)) {
	die("<p class=\"text-center\"><strong>Seleccione Moneda...</strong></p>");
}
include('../../includes/funciones.php');
include('../../includes/conn.php');
$sql = "SELECT id,
			to_char(fecha,'DD/MM/YYYY') AS fecha,
			valor
		FROM monedas_valor
		WHERE idmoneda = $id_moneda
			AND idmoneda_valor = $id_valor
		ORDER BY fecha::date ASC";
$query = pg_query($conn, $sql);
?>
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-moneda">
    <thead>
      <tr class="headings">
        <th class="column-title text-center">Fecha  </th>
        <th class="column-title text-right">Tipo/Cambio </th>
        <th class="column-title no-link last text-center"><span class="nobr">Acci&oacute;n</span>
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
	exit;
}
$act = 1;
while($fila = pg_fetch_assoc($query)) {
	$clase = (($act%2)==0)?"odd pointer":"even pointer";
?>
      <tr class="<?php print $clase ?>">
        <td class=" text-center"><?php print  ($fila['fecha']=='01/01/2000')?"N/A":$fila['fecha'] ?></td>
        <td class=" text-right"><?php print number_format($fila['valor'],2,'.',',') ?></td>
        <td class=" last text-center"><a href="#" title="Click para Eliminar"><i class="fa fa-remove" style="color: red;"></i></a>
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