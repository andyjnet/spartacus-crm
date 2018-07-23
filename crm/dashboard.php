<?php
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
/*
 select now()::date AS hoy, 
DATE_TRUNC('week', NOW())::DATE AS lunes,
date_trunc('week', NOW() - interval '1 week')::DATE AS lunes_pasado,
date_trunc('week', NOW() - interval '1 week')::date+6 AS domingo
 */

 /******** Consulta eliminada
//-- Clientes
$sql = "SELECT t.total,
          (CASE WHEN total > 0 THEN (t.semana*100/t.total) - (t.sem_pasada*100/t.total) ELSE 0 END) AS diferencia
        FROM (
          SELECT COUNT(*) AS total,
            SUM( (CASE WHEN fecha_mod::date >= date_trunc('week', NOW() - interval '1 week')::DATE 
                        AND fecha_mod::date <= date_trunc('week', NOW() - interval '1 week')::DATE+6
                   THEN 1 ELSE 0 END)
            ) AS sem_pasada,
            SUM( (CASE WHEN fecha_mod::date >= date_trunc('week', NOW())::DATE
                       THEN 1 ELSE 0 END)
            ) AS semana
          FROM clientes
          WHERE id>0
            AND (idejecutivo = $idusuario OR $usr_admin = 1)
        ) AS t";
$query = pg_query($conn, $sql);
if($row = pg_fetch_assoc($query)) {
  $total_clientes = $row['total'];
  $dif_clientes   = $row['diferencia'];
}
********/
//-- Valores / Resumen
$sql = "SELECT c.idetapa, 
          SUM((CASE WHEN c.idetapa=44 THEN c.prima_neta ELSE 0 END)) AS enviado_cotizar,
          SUM((CASE WHEN c.idetapa=46 THEN c.prima_neta ELSE 0 END)) AS cotizacion,
          SUM((CASE WHEN idetapa=44 THEN 1 ELSE 0 END)) AS cant_enviado,
          SUM((CASE WHEN idetapa=46 THEN 1 ELSE 0 END)) AS cant_cotizacion          
        FROM cotizacion c
          INNER JOIN usuarios u ON(c.idejecutivo = u.id)
        WHERE idetapa IN(44, 46)
          AND (idejecutivo = $idusuario OR $usr_admin = 1 OR u.idsupervisor = $idusuario)
        GROUP BY idetapa";
$query = pg_query($conn, $sql);
$sum_enviado    = 0;
$sum_cotizacion = 0;
$can_enviado    = 0;
$can_cotizacion = 0;  
while($row = pg_fetch_assoc($query)) {
  switch($row['idetapa']) {
    case 44:
      $sum_enviado    = $row['enviado_cotizar'];
      $can_enviado    = $row['cant_enviado'];
      break;
    case 46:
      $sum_cotizacion = $row['cotizacion'];
      $can_cotizacion = $row['cant_cotizacion'];
  }
} 


//-- Cotizaciones
$usr_permisos = $_SESSION['permisos'] ?? '';
$sql_etapas   = '';
if($usr_permisos && !$usr_admin) {
  $access = explode(",", $usr_permisos);
  foreach ($access as $item) {
    if(intval($item) > 1000) {
      $sql_etapas .= (intval($item) - 1000).',';
    }
  }
  if($sql_etapas) {
    $sql_etapas = substr($sql_etapas, 0, strlen($sql_etapas) - 1);
    $sql_etapas = " OR c.idetapa IN($sql_etapas) ";
  } 
} else {
  $access = array("");
}

$sql_campaign = '';
if($id_campaign) {
  $sql_campaign = " AND (c.idcampaign = $id_campaign  OR c.idetapa IN(50, 51, 44, 46, 47, 48)) ";
}
$sql = "SELECT t.total,
          (CASE WHEN total > 0 THEN (t.semana*100/t.total) - (t.sem_pasada*100/t.total) ELSE 0 END) AS diferencia
        FROM (
          SELECT COUNT(*) AS total,
            SUM( (CASE WHEN c.fecha_reg::date >= date_trunc('week', NOW() - interval '1 week')::DATE 
                        AND c.fecha_reg::date <= date_trunc('week', NOW() - interval '1 week')::DATE+6
                   THEN 1 ELSE 0 END)
            ) AS sem_pasada,
            SUM( (CASE WHEN c.fecha_reg::date >= date_trunc('week', NOW())::DATE
                      THEN 1 ELSE 0 END)
            ) AS semana
          FROM cotizacion c
            INNER JOIN usuarios u ON(c.idejecutivo = u.id)
          WHERE c.id>0 and c.estado > -1
            AND (c.idejecutivo = $idusuario OR $usr_admin = 1 OR u.idsupervisor = $idusuario)
            $sql_campaign
            $sql_etapas
        ) AS t";
$query = pg_query($conn, $sql);
if($row = pg_fetch_assoc($query)) {
  $total_cotizacion = $row['total'];
  $dif_cotizaciones = $row['diferencia'];
}
?>
<!-- Contenido de la página -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row top_tiles">
      <div class="animated flipInY col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-calculator"></i></div>
          <div class="count"><?php print $total_cotizacion ?></div>
          <h3>Cotizaciones</h3>
          <p><span class="count_bottom"><i class="<?php print ($dif_cotizaciones > 0)?'green':'red' ?>">
            <i class="fa fa-sort-<?php print ($dif_cotizaciones > 0)?'asc':'desc' ?>"></i>
            <?php print $dif_cotizaciones ?>% </i> Desde la semana pasada</span>
          </p>          
        </div>
      </div>      
      <div class="animated flipInY col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-send"></i></div>
          <div class="count"><?php print $can_enviado ?></div>
          <h3>Enviado a Cotizar</h3>
          <p><span class="count_bottom">
            <strong><?php print number_format($sum_enviado,"2",",",".") ?></strong> UF</span>
          </p>
        </div>
      </div>
      <div class="animated flipInY col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-money"></i></div>
          <div class="count"><?php print $can_cotizacion ?></div>
          <h3>Por Cerrar</h3>
          <p><span class="count_bottom">
            <strong><?php print number_format($sum_cotizacion,"2",",",".") ?></strong> UF</span>
          </p>
        </div>
      </div>      

    </div>
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
          <!--  Grafico -->
          <div class="x_content" id="main" style="height:400px">
          </div>
          <!--/ Grafico -->
        </div>
      </div>
    </div>    
</div>
<!-- /Contenido de la página -->
<?php
include('includes/dash-footer.php');
//-- Obtenemos los datos para el grafico
$sql = "SELECT e.descripcion FROM etapas_venta e ORDER BY e.orden";
$query = pg_query($conn, $sql);
$leyenda = '';
while($row = pg_fetch_assoc($query)) {
  $leyenda .= "'".$row['descripcion']."',";
}
if(isset($leyenda)) {
  $leyenda = substr($leyenda, 0, strlen($leyenda)-1);
} else {
  $leyenda = "'Sin Datos'";
}

$sql = "SELECT e.descripcion AS etapa, COUNT(*) AS cantidad
        FROM cotizacion c
          INNER JOIN etapas_venta e ON(c.idetapa=e.id)
          INNER JOIN usuarios u ON(c.idejecutivo = u.id)
        WHERE c.estado > -1
          AND (c.idejecutivo = $idusuario
            OR $usr_admin = 1
            OR u.idsupervisor = $idusuario)
          $sql_campaign
          $sql_etapas
        GROUP BY e.descripcion, e.orden
        ORDER BY e.orden";
$query = pg_query($conn, $sql);      
?>
<!-- ECharts import -->
<script src="../js/echarts.js"></script>    
<!-- Mostrar/Ocultar logo Spartacus -->
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
            'echarts/chart/pie' // require the specific chart type
        ],
        function (ec) {
            // Initialize after dom ready
            var myChart = ec.init(document.getElementById('main')); 
            
            option = {
                title : {
                    text: 'Estadistica de Rendimiento',
                    subtext: 'distribución segun etapa',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
            legend: {
                    orient : 'vertical',
                    x : 'left',
                    data:[<?php print $leyenda ?>]
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
                        name:'Cotizaciones',
                        type:'pie',
                        radius : '60%',
                        center: ['50%', '60%'],
                        data:[
<?php
if(pg_num_rows($query) === 0) {
  print "{value:1, name:'Sin Datos'},";
} else {
  while($dato = pg_fetch_assoc($query)) {
?>
{value:<?php print $dato['cantidad'] ?>, name:'<?php print $dato['etapa'] ?>'},
<?php
  }
}
?>
                        ]
                    }
                ]
            };
            // Load data into the ECharts instance 
            myChart.setOption(option); 
        }
    );        
</script>