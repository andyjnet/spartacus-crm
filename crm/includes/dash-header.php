<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
include_once("../includes/tools.php");
$username	  = isset($_SESSION['usuario'])?$_SESSION['usuario']:'';
$idusuario	  = isset($_SESSION['uid'])?$_SESSION['uid']:0;
$usr_nombre	  = isset($_SESSION['nombre'])?$_SESSION['nombre']:'';
$usr_admin	  = $_SESSION['admin'] ?? 0;
$usr_permisos = $_SESSION['permisos'] ?? '';
$id_campaign  = $_SESSION['campaign'] ?? 0;
$valor_uf	  = $_SESSION['valor_uf'] ?? 0;
if($usr_permisos) {
	$access    = explode(",", $usr_permisos);
	$cotiza    = false;
	$configura = false;
	//-- bof Recorremos los permisos 
	foreach ($access as $item) {
		//-- Determinar si se hace visible la opcion de Cotizacion
		if(intval($item) > 1000) {
			$cotiza = true;
		}
		//-- Determinar si se hace visible el titulo de Configuracion
		if(intval($item) >= 13 && intval($item) <= 23) {
			$configura = true;
		}
		if($cotiza && $configura) break;
	}
	//-- eof Recorremos los permisos
	
	
} else {
	$access = array("");
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"      content="width=device-width, initial-scale=1">
    <meta name="author" 	   content="Spartacus Group" />
    <meta name="description"   content="Sistema personalizado para gestion de relacion con los clientes (CRM) y automatizacion de la fuerza de ventas (SFA)" />
    <meta name="keywords"      content="spartacus,corredores,seguros,SpA,group,crm,sfa,sociedad,por,acciones,asesores,vehiculos,vida,chile" />
    <meta name="Resource-type" content="Document" />    

    <title>Spartacus group</title>
    <link rel="shortcut icon" type="image/png" href="images/favicon.png" />
    
    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
<?php
if(isset($css_form)) {
?>
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-wysiwyg -->
    <link href="../vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- Switchery -->
    <link href="../vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    <!-- starrr -->
    <link href="../vendors/starrr/dist/starrr.css" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
	<!-- Ajax Loading effect -->
	<link href="../css/loading.min.css" rel="stylesheet" type="text/css">	
<?php
}
if(isset($css_table)) {
?>
    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">	
<?php
}
if(isset($notify)) {
?>
    <!-- PNotify -->
    <link href="../vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="../vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="../vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
<?php
}
?>
    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">  
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="dashboard.php" class="site_title">
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
					  <a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a>
					</li>
<?php
if($usr_admin == 1 || comprueba($usr_permisos, "2")
				   || comprueba($usr_permisos, "3")
				   || comprueba($usr_permisos, "4")
				   || comprueba($usr_permisos, "5") ) {
?>
					<li>
					  <a href="clientes.php"><i class="fa fa-users"></i> Clientes</a>
					</li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "-1")
				   || comprueba($usr_permisos, "-2")
				   || comprueba($usr_permisos, "8")
				   || comprueba($usr_permisos, "9")
				   || comprueba($usr_permisos, "10")
				   || comprueba($usr_permisos, "11")
				   || $cotiza === true) {
?>
					<li>
					  <a href="cotizacion.php"><i class="fa fa-calculator"></i> Cotizaciones</a>
					</li>
<?php
}
?>
				  </ul>
				</div>
				
				<div class="menu_section">
				  <h3>Informes</h3>
				  <ul class="nav side-menu">				  
<?php
if($usr_admin == 1) {
?>
					<li>
					  <a href="informe001.php"><i class="fa fa-line-chart"></i> Gestion</a>
					</li>  
<?php
}
?>
					<li>
					  <a href="informe002.php"><i class="fa fa-money"></i> Comisiones</a>
					</li>
					
				  </ul>		
				</div>	
              <div class="menu_section">
<?php
if($usr_admin == 1 || $configura) {
?>
                <h3>Configuraci&oacute;n</h3>
<?php
}
?>
                <ul class="nav side-menu">
<?php
if($usr_admin == 1 || comprueba($usr_permisos, "-3")
				   || comprueba($usr_permisos, "13")
				   || comprueba($usr_permisos, "14")
				   || comprueba($usr_permisos, "15")
				   || comprueba($usr_permisos, "16")
				   || comprueba($usr_permisos, "17")
				   || comprueba($usr_permisos, "18")
				   || comprueba($usr_permisos, "19")
				   || comprueba($usr_permisos, "20")) {
?>
                  <li><a><i class="fa fa-database"></i> Tablas de Valores <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
<?php
if($usr_admin == 1 || comprueba($usr_permisos, "13")) {
?>
					  <li><a href="cargos.php">Cargos</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "14")) {
?>
					  <li><a href="niveles.php">Categorias / Ejecutivos</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "26")) {
?>					  
					  <li><a href="companias.php">Compa&ntilde;ias de seguros</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "15")) {
?>					  
					  <li><a href="corredores.php">Corredores</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "16")) {
?>						  
					  <li><a href="embudo.php">Embudo de Ventas</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "17")) {
?>	
					  <li><a href="fases_cl.php">Fases de Clientes</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "25")) {
?>	
					  <li><a href="formas_pago.php">Formas de pago</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "27")) {
?>	
					  <li><a href="campaign.php">Gestionar Campa&ntilde;as</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "18")) {
?>						  
                      <li><a href="currency.php">Monedas</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "24")) {
?>
					  <li><a href="producto.php">Productos</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "19")) {
?>						  
					  <li><a href="ramos.php">Ramos</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "20")) {
?>						  
					  <li><a href="sucursales.php">Sucursales</a></li>
<?php
}
?>
                    </ul>
                  </li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "-4")
				   || comprueba($usr_permisos, "22")
				   || comprueba($usr_permisos, "23") ) {
?>
                  <li><a><i class="fa fa-user"></i> Usuarios <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
<?php
if($usr_admin == 1 || comprueba($usr_permisos, "22")) {
?>						
                      <li><a href="ejecutivos.php">Gestionar</a></li>
<?php
}
if($usr_admin == 1 || comprueba($usr_permisos, "23")) {
?>
                      <li><a href="permisos.php">Permisos</a></li>
<?php
}
?>
                    </ul>
                  </li>
<?php
}
?>
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
                    <?php print $usr_nombre ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="perfil.php">Perfil</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i>Cerrar Sesi&oacute;n</a></li>
                  </ul>
                </li>
				<li class="">
					<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					 UF ($): <strong><?php print number_format($valor_uf, 2, ",", ".") ?></strong>
					</a>
				</li>				
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->