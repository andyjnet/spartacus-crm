<?php
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
$ide = $_POST['ide'] ?? 0;
$sWhere = "";
if($ide) {
    $sWhere = " WHERE c.idejecutivo = $ide ";
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
            <form id="frm-informe" data-parsley-validate class="form-horizontal form-label-left" name="frm-informe" method="post">
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="ejecutivo">
                  Seleccione Ejecutivo
                </label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <select id="ejecutivo" class="form-control" required name="ide">
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
?>
                  </select>   
                </div>                       
              </div>               
            </form> 
          </div>
        
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
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
              <!--  Grafico -->
              <div class="x_content" id="main" style="height:300px">
              </div>
              <!--/ Grafico -->
            </div>
        </div>
<?php
//-- Valores Ratio del Ejecutivo Seleccionado
$sql = "SELECT SUM((CASE WHEN c.idetapa=50 THEN 1 ELSE 0 END)) AS volver_llamar, 
            SUM((CASE WHEN c.idetapa=43 THEN 1 ELSE 0 END)) AS contacto,
            SUM((CASE WHEN c.idetapa=44 THEN 1 ELSE 0 END)) AS enviado_cotizar,
            SUM((CASE WHEN c.idetapa=46 THEN 1 ELSE 0 END)) AS cotizado,
            SUM((CASE WHEN c.idetapa=49 THEN 1 ELSE 0 END)) AS eliminar,
            SUM((CASE WHEN c.idetapa=51 THEN 1 ELSE 0 END)) AS espera_respuesta,
            SUM((CASE WHEN c.idetapa=52 THEN 1 ELSE 0 END)) AS mal_contacto,
            SUM((CASE WHEN c.idetapa=47 THEN 1 ELSE 0 END)) AS acepta,
            SUM((CASE WHEN c.idetapa=45 THEN 1 ELSE 0 END)) AS rechaza,
            SUM((CASE WHEN c.idetapa=48 THEN 1 ELSE 0 END)) AS venta
        FROM cotizacion c
            INNER JOIN clientes_contactos cc ON(cc.idcliente = c.idcliente)
            INNER JOIN etapas_venta e ON(c.idetapa=e.id)
        $sWhere
        GROUP BY c.idejecutivo";       
$query = pg_query($conn, $sql);
$ratio1 = 0;
$ratio2 = 0;
$ratio3 = 0;
$ratio4 = 0;
$ratio5 = 0;
if($row = pg_fetch_assoc($query)) {
    $cRatio1 = $row['volver_llamar'] + $row['enviado_cotizar'] + $row['cotizado'] + $row['eliminar'] + $row['espera_respuesta'];
    $cRatio1 += $row['mal_contacto'] + $row['acepta'] + $row['rechaza'] + $row['venta'];
    $cRatio2 = $row['volver_llamar'] + $row['espera_respuesta'];
    
    $ratio1 = $row['contacto'] ? $cRatio1 / $row['contacto'] : 0;
    $ratio2 = $cRatio2 ? $row['enviado_cotizar'] / $cRatio2 : 0;
    
    $ratio3 = $row['enviado_cotizar'] ? $row['cotizado'] / $row['enviado_cotizar'] : 0;
    $ratio4 = $row['cotizado'] ? $row['venta'] / $row['cotizado'] : 0; 
    $ratio5 = $row['contacto'] ? $row['venta'] / $row['contacto'] : 0;
    $ratio1 = number_format($ratio1 * 100,2).'%';
    $ratio2 = number_format($ratio2 * 100,2).'%';
    $ratio3 = number_format($ratio3 * 100,2).'%';
    $ratio4 = number_format($ratio4 * 100,2).'%';
    $ratio5 = number_format($ratio5 * 100,2).'%';
}
?>
        <div class="col-md-6 col-sm-6 col-xs-12">
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
                <!--  Contenido -->
                <div class="x_content" id="ratios" style="height:300px">
                    <form id="frm-ratio" class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12 text-left" for="ratio1">
                              Eficiencia conversi&oacute;n
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="ratio1" type="text" class="form-control"
                                     value="<?php print $ratio1 ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12 text-left" for="ratio2">
                              Eficiencia telefonica
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="ratio2" type="text" class="form-control"
                                     value="<?php print $ratio2 ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12 text-left" for="ratio3">
                              Tiempos en cotizaci&oacute;n
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="ratio3" type="text" class="form-control"
                                     value="<?php print $ratio3 ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12 text-left" for="ratio4">
                              Eficacia
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="ratio4" type="text" class="form-control"
                                     value="<?php print $ratio4 ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-6 col-sm-6 col-xs-12 text-left" for="ratio5">
                              Valoracion empresa
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input id="ratio5" type="text" class="form-control"
                                     value="<?php print $ratio5 ?>">
                            </div>
                        </div>                          
                    </form>
                </div>
                <!--/ Contenido -->
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
    // configure for module loader
    require.config({
        paths: {
            echarts: '../js'
        }
    });
    require(
        [
            'echarts',
            'echarts/chart/funnel' // require the specific chart type
        ],
        function (ec) {
            // Initialize after dom ready
            var myChart = ec.init(document.getElementById('main'), 'blue'); 
            
            option = {
                title : {
                    text: 'Estadistica de gestion',
                    subtext: 'distribución segun etapa',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },                
                toolbox: {
                    show :true,
                    feature : {
                        mark : {show: false},
                        dataView : {show: true, readOnly: false, title: 'Ver datos',
                            lang: ['Vista de Datos', 'Cerrar', 'Refrescar']},
                        restore : {show: true, title: 'Reiniciar'},
                        saveAsImage : {show: true, title: 'Guardar Imagen'}
                    }
                },
                calculable : true,
                series : [
                  {
                      name:'Embudo de Ventas',
                      type:'funnel',
                      x: '2.5%',
                      y: 60,
                      //x2: 80,
                      y2: 60,
                      width: '95%',
                      min: 0,
                      max: 100,
                      minSize: '0%',
                      maxSize: '100%',
                      sort : 'descending', // 'ascending', 'descending'
                      gap : 10,
                      itemStyle: {
                          normal: {
                              borderColor: '#fff',
                              borderWidth: 1,
                              label: {
                                  show: true,
                                  position: 'inside'
                              },
                              labelLine: {
                                  show: false,
                                  length: 10,
                                  lineStyle: {
                                      width: 1,
                                      type: 'solid'
                                  }
                              }
                          },
                          emphasis: {
                              borderColor: 'red',
                              borderWidth: 5,
                              label: {
                                  show: true,
                                  formatter: '{b}:{c}%',
                                  textStyle:{
                                      fontSize:20
                                  }
                              },
                              labelLine: {
                                  show: true
                              }
                          }
                      },
                      data:[
<?php
$sql = "SELECT e.descripcion,
            SUM(COUNT(*)) OVER() AS general,
            (COUNT(*)/SUM(COUNT(*))  OVER())::numeric(9,5) * 100 AS valor,
            COUNT(*) AS cantidad
        FROM cotizacion c 
            INNER JOIN clientes_contactos cc ON(c.idcliente = cc.idcliente)
            INNER JOIN etapas_venta e ON(c.idetapa=e.id)
        $sWhere
        GROUP BY e.descripcion
        ORDER BY 3 DESC";
$query = pg_query($conn, $sql);
$json = "";
while($row = pg_fetch_assoc($query)) {
    $valor = number_format($row['valor'],2);
    $des   = $row['descripcion']." (".$row['cantidad'].")";
    $json .= "{value:$valor, name:'$des'},";
}
if(!$json) $json = "{value:0, name: 'Sin Datos'}";
print($json);
?>
                      ]
                  }
                ]
            };
            // Load data into the ECharts instance 
            myChart.setOption(option);
        }
    );
    $(document).ready(function() {
        $('#ejecutivo').change(function() {
            this.form.submit();
        });
        $("[id^='ratio']").focus(function(){
            this.blur();
        });        
    });
</script>