<?php
$css_form  = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
$ide   = $_POST['ejecutivo'] ?? 0;
$desde = $_POST['fecha1'] ?? '';
$hasta = $_POST['fecha2'] ?? '';
$sWhere = "";
$dis = "";
if(!$usr_admin) {
    $ide = $idusuario;
    $dis = ' disabled = "disabled" ';
}
if($ide) {
    $sWhere = " AND c.idejecutivo = $ide ";
} 
?>
<!-- Contenido de la página -->
<div class="right_col" role="main">
    <!-- /top tiles -->
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          
          <div class="x_content">
            <form id="frm-informe" action= "" data-parsley-validate class="form-horizontal form-label-left" name="frm-informe" method="post">
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="ejecutivo">
                  Seleccione Ejecutivo
                </label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <select id="ejecutivo" name="ejecutivo" class="form-control" required name="ide"<?php print $dis ?>>
                    <option value="">Seleccione...</option>
<?php
$sql = "SELECT id, CONCAT(nombre, ' ', apellidos) AS descripcion
      FROM usuarios
      WHERE estado=1
        AND idcargo > 0
      ORDER BY nombre, apellidos";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
    if($ide == $row['id']) $sel = " selected";
    print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
    $sel = "";
}

$mes_ini = new DateTime("first day of last month");
$mes_fin = new DateTime("last day of last month");

$desde = $desde ?? $mes_ini->format('d/m/Y'); 
$hasta = $hasta ?? $mes_fin->format('d/m/Y');
?>
                  </select>   
                </div>                       
              </div>
              
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="fecha1">
                  Fechas
                </label>
                <div class="control-group">
                  <div class="controls">
                    <div class="col-md-4 col-sm-4 col-xs-12 xdisplay_inputx form-group has-feedback">
                      <input type="text" class="form-control" id="desde"
                             name="fecha1" placeholder="Desde"
                             aria-describedby="inputSuccess2Status"
                             value="<?php print $desde ?>">
                      <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                      <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                    </div>
                  </div>
                </div>
                <div class="control-group">
                  <div class="controls">
                    <div class="col-md-4 col-sm-4 col-xs-12 xdisplay_inputx form-group has-feedback">
                      <input type="text" class="form-control" id="hasta"
                             name="fecha2" placeholder="Hasta"
                             aria-describedby="inputSuccess2Status"
                             value="<?php print $hasta ?>">
                      <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                      <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-12 text-right">
                  <button type="submit" class="btn btn-success">Aceptar</button>
                </div>                
            </div>
              
            </form> 
          </div>
        
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Informe de Comisiones</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                  <div class="clearfix"></div>
                </div>
              <!--  Tabla Resultado -->
<?php
$fecha1 = "'".substr($desde, 6, 4).'-'.substr($desde, 3, 2).'-'.substr($desde, 0, 2)."'";
$fecha2 = "'".substr($hasta, 6, 4).'-'.substr($hasta, 3, 2).'-'.substr($hasta, 0, 2)."'";
$sWhere .= " AND c.vigencia BETWEEN $fecha1 AND $fecha2 ";
$sql = "SELECT c.poliza, cl.nombre,
            TO_CHAR(c.vigencia, 'DD/MM/YYYY') AS vigencia, 
            c.prima_actual AS pbruta,
            c.prima_neta * 0.19 AS iva,
            c.prima_neta,
            c.comision_corredor::numeric(5,2) AS comision_corredor,
            (c.prima_neta * comision_corredor/100)::numeric(14,3) AS monto_comision,
            (c.prima_neta * comision_corredor/100 * 0.15)::numeric(14,3) AS csa,
            (c.prima_neta * comision_corredor/100 * 0.85)::numeric(14,3) AS a_comision,
            c.comision::integer AS intermediacion,
            (c.prima_neta * comision_corredor/100 * 0.85 * (c.comision/100))::numeric(14,2) AS remuneracion
        FROM cotizacion c
            INNER JOIN clientes cl ON(c.idcliente = cl.id)
        WHERE c.idetapa = 48
        $sWhere
        ORDER BY c.vigencia::date";
$query = pg_query($sql);
?>              
              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action" id="tbl-clientes">
                    <thead>
                      <tr class="headings">
                        <th class="column-title text-left">Poliza  </th>
                        <th class="column-title text-left">Cliente </th>
                        <th class="column-title text-left">Emisi&oacute;n </th>
                        <th class="column-title text-left">P.Bruta </th>
                        <th class="column-title text-left">IVA </th>
                        <th class="column-title text-left">P.Neta </th>
                        <th class="column-title text-left">% Com. </th>
                        <th class="column-title text-left">Monto </th>
                        <th class="column-title text-left">Dsto.CSA </th>
                        <th class="column-title text-left">A Com. </th>
                        <th class="column-title text-left">% Inter. </th>
                        <th class="column-title no-link last text-center"><span class="nobr">$ Com.</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
<?php                        
if(pg_num_rows($query) == 0) {
?>                        
                      <tr class="even pointer">
                        <td class="text-center" colspan="12">No hay Registros que coincidan con su b&uacute;squeda</td>
                      </tr>
<?php
}
$tbruta = 0;
$tiva   = 0;
$tneta  = 0;
$total  = 0;
while($fila = pg_fetch_assoc($query)) {
?>
    <tr class="<?php print $clase ?>">
      <td class=" text-left"><strong><?php print  $fila['poliza'] ?></strong></td>
      <td class=" text-left"><?php print $fila['nombre'] ?></td>
      <td class=" text-left"><?php print $fila['vigencia'] ?></td>
      <td class=" text-right"><?php print $fila['pbruta'] ?></td>
      <td class=" text-right"><?php print $fila['iva'] ?></td>
      <td class=" text-right"><?php print $fila['prima_neta'] ?></td>
      <td class=" text-right"><?php print $fila['comision_corredor'] ?></td>
      <td class=" text-right"><?php print $fila['monto_comision'] ?></td>
      <td class=" text-right" style="color: red"><?php print $fila['csa'] ?></td>
      <td class=" text-right"><strong><?php print $fila['a_comision'] ?></strong></td>
      <td class=" text-right"><?php print $fila['intermediacion'] ?></td>
      <td class=" last text-right" style="color: green"><strong><?php print $fila['remuneracion'] ?></strong></td>
    </tr>
<?php
    $tbruta += $fila['pbruta'];
    $tiva   += $fila['iva'];
    $tneta  += $fila['prima_neta'];
    $total  += $fila['remuneracion'];
}
if($tbruta || $tneta || $total) {
    $total = $total * $valor_uf;
?>
    <tr class="<?php print $clase ?>">
      <td class=" text-left">&nbsp;</td>
      <td class=" text-left">&nbsp;</td>
      <td class=" text-left">&nbsp;</td>
      <td class=" text-right"><strong><?php print number_format($tbruta,2,",",".") ?></strong></td>
      <td class=" text-right"><strong><?php print number_format($tiva,2,",",".") ?></strong></td>
      <td class=" text-right"><strong><?php print number_format($tneta,2,",",".") ?></strong></td>
      <td class=" text-right">&nbsp;</td>
      <td class=" text-right">&nbsp;</td>
      <td class=" text-right" colspan="2"><strong>Valor Bruto ($):</strong></td>
      <td class=" last text-right" style="color: green" colspan="2"><strong><?php print number_format($total,2,",",".") ?></strong></td>
    </tr>
<?php
}
?>
                    </tbody>
                  </table>
                </div>      
              </div>
              <!--/ Tabla Resultado -->
            </div>
        </div>       
    </div>     
</div>
<!-- /Contenido de la página -->
<?php
include('includes/dash-footer.php');
?>
<!-- ECharts import -->
<script src="../js/echarts.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#desde, #hasta').daterangepicker({
          "locale": {
                  "format": "DD/MM/YYYY",
                  "separator": " - ",
                  "applyLabel": "Aplicar",
                  "cancelLabel": "Cancelar",
                  "fromLabel": "de",
                  "toLabel": "a",
                  "customRangeLabel": "Personalizado",
                  "weekLabel": "S",
                  "daysOfWeek": [
                      "Do",
                      "Lu",
                      "Ma",
                      "Mi",
                      "Ju",
                      "Vi",
                      "Sa"
                  ],
                  "monthNames": [
                      "Enero",
                      "Febrero",
                      "Marzo",
                      "Abril",
                      "Mayo",
                      "Junio",
                      "Julio",
                      "Agosto",
                      "Septiembre",
                      "Octubre",
                      "Noviembre",
                      "Diciembre"
                  ],
                  "firstDay": 1
          },
          singleDatePicker: true,
          singleClasses: "picker_4"
        }, function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });        
    });
</script>