<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"          content="width=device-width, initial-scale=1">
    <meta name="author" 	     		 content="Spartacus Group" />
    <meta name="description"   		 content="Sistema personalizado para gestion de relacion con los clientes (CRM) y automatizacion de la fuerza de ventas (SFA)" />
    <meta name="keywords"      		 content="spartacus,corredores,seguros,SpA,group,crm,sfa,sociedad,por,acciones,asesores,vehiculos,vida,chile" />
    <meta name="Resource-type" 		 content="Document" />    

    <title>Spartacus group</title>
    <link rel="shortcut icon" type="image/png" href="images/favicon.png" />
    
    <!-- Bootstrap -->
    <link href="../../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../vendors/nprogress/nprogress.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../../build/css/custom.min.css" rel="stylesheet">  
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.php" class="site_title">
                <span class="logo-mini-hide" id="logo"><img src="images/logo_48.png"></span> <span>Spartacus Group</span>
              </a>
            </div>

            <div class="clearfix"></div>

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li>
                    <a href="dashboard.php"><i class="fa fa-home"></i> Inicio</a>
                  </li>
                  <li><a><i class="fa fa-users"></i> Clientes <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="form.html">Gestionar</a></li>
                    </ul>
                  </li>
                </ul>
              </div>
              
              <div class="menu_section">
                <h3>Configuraci&oacute;n</h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-th-list"></i> Tablas de Valores <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="#">Unidad de Fomento (UF)</a></li>
                    </ul>
                  </li>                  
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Configuraci&oacute;n">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Bloquear">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Cerrar Sesi&oacute;n" href="logout.php">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    Nombre de Usuario
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="perfil.php">Perfil</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i>Log Out</a></li>
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- Contenido de la página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="clearfix"></div>
            
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
        </div>
        <!-- /Contenido de la página -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Spartacus Group - Todos los derechos reservados &copy; 2017</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../../vendors/jquery/dist/jquery.min.js"></script>     
    <!-- Bootstrap -->
    <script src="../../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../../vendors/nprogress/nprogress.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../../build/js/custom.min.js"></script>
    <!-- ECharts import -->
    <script src="js/echarts.js"></script>    
    <!-- Mostrar/Ocultar logo Spartacus -->
    <script type="text/javascript">
        // configure for module loader
        require.config({
            paths: {
                echarts: './js'
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
                        text: 'Estadistica actual de Cotizaciones',
                        subtext: 'Distribución segun etapa',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                legend: {
                       orient : 'vertical',
                       x : 'left',
                       data:['Estado1','Estado2','Estado3','Estado4','Otro']
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
                                {value:398, name:'Estado1'},
                                {value:310, name:'Estado2'},
                                {value:234, name:'Estado3'},
                                {value:135, name:'Estado4'},
                                {value:1548, name:'Otro'}
                            ]
                        }
                    ]
                };
                // Load data into the ECharts instance 
                myChart.setOption(option); 
            }
        );        
    </script>
    <script>
    $(document).ready(function(){
        $("#menu_toggle").click(function(){
          var class_anchor = $('#logo').attr('class');
          if(class_anchor == 'logo-mini-hide') {
              $("#logo").removeClass("logo-mini-hide").addClass('logo-mini-show');
              $("#logo").show();
          } else {
              $("#logo").removeClass("logo-mini-show").addClass('logo-mini-hide');
              $("#logo").hide();
          }          
        });
    });
    </script>     
  </body>
</html>
