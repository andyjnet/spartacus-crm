<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	die('<center><img src="../img/logo_sparta.png" width="150px"><h4>Acceso incorrecto haga click [<a href="index.php">aqui</a>] para entrar al sistema</h4></center>');
}
$uid = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="es">
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
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
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
    <!-- PNotify -->
    <link href="../vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="../vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="../vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
    <script>var RutVar = false;</script>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="" class="site_title">
                <img src="images/logo_48.png">
              </a>
            </div>

            <div class="clearfix"></div>

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">


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
        <div class="top_nav" style="visibility: hidden;">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"></a>
              </div>
              <ul class="nav navbar-nav navbar-right">
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- Contenido de la página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_center">
                <h3>SPARTACUS SEGUROS GENERALES SpA</h3>
              </div>

            </div>

            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" id="div-frm">
                  <div class="x_title">
                    <h2>Registro <small>Formulario de autorizaci&oacute;n de corretaje</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="" id="cierra-frm"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form id="frm-cliente" data-parsley-validate class="form-horizontal form-label-left" name="frm_cliente" method="post">                  
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">
                          Nombre <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Nombre o razon social" id="nombre" required="required">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="apellido1">
                          Apellido Paterno <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Indique primer apellido" id="apellido1" required="required">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="apellido2">
                          Apellido Materno <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Indique segundo apellido" id="apellido2" required="required">
                        </div>
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="rut">
                          RUT <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text"
                                 class="form-control"
                                 style="text-transform:uppercase;"
                                 id="rut" placeholder="ingrese RUT (ej: 00.000.000-0)"
                                 maxlength="12"
                                 required="required"
                                 data-parsley-error-message="el Rut es requerido para continuar">
                        </div>                        
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="rut-serie">
                          N Serie Carn&eacute; 
                          <a href="#" data-placement="bottom" title="Click para ver ejemplo"
                             data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-eye" style="color: red;"></i>
                          </a>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Número al reverso del carné de identidad (click en ícono rojo para muestra)" id="rut-serie" required="required">
                        </div>
                        <!-- Presentar imagen de muestra -->
                        <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="myModal">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel2">Imagen de muestra</h4>
                              </div>
                              <div class="modal-body">
                                <center>
                                <img src="images/ci_reverso.jpg" class="img-responsive">
                                </center>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>                        
                        <!--/ Presentar imagen de muestra -->                        
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">
                          Email <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                          <input type="email" data-parsley-trigger="change" class="form-control" id="email" placeholder="Email de contacto" required="required">
                          <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span>
                        </div>                        
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          Tel&eacute;fono <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="telefono" placeholder="Telefono fijo" required="required">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          M&oacute;vil <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="movil" placeholder="Movil" required="required">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>

                      <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="direccion">
                            Direcci&oacute;n <span class="required">*</span>
                          </label>
                          <div class="col-md-10 col-sm-10 col-xs-12">
                            <textarea id="direccion" class="form-control" name="direccion" rows="2" placeholder="Direccion de contacto" required="required"></textarea>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="comentarios">
                            Comentarios
                          </label>
                          <div class="col-md-10 col-sm-10 col-xs-12">
                            <textarea id="comentarios" class="form-control" name="comentarios" rows="2" placeholder="Ingrese comentarios o notas relacionadas al cliente"></textarea>
                          </div>
                      </div>
                      <div class="ln_solid"></div>                      
                      <p align="justify" style="font-size: medium; text-indent: 40px;">Autorizo a Spartacus Seguros Generales, RUT 76.713.622-6, por si misma, o actuando a trav&eacute;z de su corredor, don Carlos Eduardo Silva Aldunate, RUT 7.077.388-0, para que, a contar del d&iacute;a antes de la renovaci&oacute;n de mis p&oacute;lizas de seguros, asuma la completa administraci&oacute;n de &eacute;stas, respecto de toda compa&ntilde;&iacute;a de seguros en la cual tenga productos vigentes. Esta autorizaci&oacute;n NO permite al mandatario a emitir, cambiar o dar de baja ning&uacute;n producto de seguros que yo tenga actualmente contratado.</p>
                      <p align="justify" style="font-size: medium; text-indent: 40px;">Esta autorizaci&oacute;n ser&aacute; verificada mediante un correo electr&oacute;nico que se env&iacute;a en forma autom&aacute;tica al cliente, por medio del cu&aacute;l &eacute;l acepta que los datos y el mandato contenido en el presente documento electr&oacute;nico son ver&iacute;dicos y expresan su voluntad.</p>
                      <div class="ln_solid"></div>
                      <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                        <button type="reset" class="btn btn-default" id="resetFrm">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="validar();">Guardar</button>
                      </div>
                      <!-- Dialogo Modal de confirmacion -->
                      <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="modConfirma">
                        <div class="modal-dialog">
                          <div class="modal-content">
            
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                              </button>
                              <h4 class="modal-title" id="myModalLabel2">Confirme...</h4>
                            </div>
                            <div class="modal-body">
                              <h4>Los datos suministrados son correctos (S/N)?</h4>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                              <button type="button" class="btn btn-success" name="btnGuardar" id="btnGuardar">Si</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!--/ Dialogo Modal de confirmacion -->	                      
                    </form>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div id="historial"></div>
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
    <script src="../vendors/jquery/dist/jquery.min.js"></script>     
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-wysiwyg -->
    <script src="../vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="../vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="../vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="../vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="../vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="../vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="../vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="../vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- jquery.inputmask -->
    <script src="../vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>    
    <!-- starrr -->
    <script src="../vendors/starrr/dist/starrr.js"></script>
    <!-- Loading effect -->
    <script src="../js/jquery.loading.min.js"></script>    
    <!-- PNotify -->
    <script src="../vendors/pnotify/dist/pnotify.js"></script>
    <script src="../vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="../vendors/pnotify/dist/pnotify.nonblock.js"></script>
    
    <!-- Validacion de RUT Chileno -->
    <script src="../js/jquery.Rut.js" type="text/javascript"></script>    
    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>
    <!-- Mostrar/Ocultar logo Spartacus -->
    <script>
      $(document).ready(function(){
        $("#menu_toggle").trigger("click");
        $("#rut").keypress(validate);
        $('#rut').Rut({
          on_error  : function() { RutVar = false; },
          on_success: function() { RutVar = true;  }
        });
        $("#resetFrm").click(function() {
          $('#frm-cliente').parsley().reset();
        });
        $("#btnGuardar").click(function() {
          $("#modConfirma").modal('hide');
          $.ajax({type: 'POST',
            url: "ajax/form01.php",
            async: false,
            /* Mostrar splash de espera mientras llega respuesta del script php */
            beforeSend:
              function() {
                $.showLoading({name: 'jump-pulse',allowHide: false});
              },
            data: {
                'nombre'     : document.getElementById("nombre").value,
                'apellido1'  : document.getElementById("apellido1").value,
                'apellido2'  : document.getElementById("apellido2").value,
                'rut'        : document.getElementById("rut").value,
                'rut-serie'  : document.getElementById("rut-serie").value,
                'email'      : document.getElementById("email").value,
                'telefono'   : document.getElementById("telefono").value,
                'movil'      : document.getElementById("movil").value,
                'direccion'  : document.getElementById("direccion").value,
                'comentarios': document.getElementById("comentarios").value
                },
            /* Colocar respuesta del script php en el marco DIV indicado */
            success:
              function(result){
                $("#historial").html(result);
                //activatbl("#tbl-clientes");
                $.hideLoading();              
              }
          });
        });        
      });
      /* Validar campo rut para permitir solo numeros, punto y guion */
      function validate(evt) {
         var key = window.event ? evt.keyCode : evt.which;
         //if (key === 0 || key === 8 || key === 46 || key === 45) {
         if ([0,8,13,45,46,75,107].indexOf(key) > -1) {
           if((key == 75 || key == 107) && $("#rut").val().toUpperCase().indexOf("K")  > -1) return false;
           return true;
         } else if ( key < 48 || key > 57 ) {
           return false;
         } else {
           return true;
         }
      }
      /* Validar formulario de registro de Clientes */
      function validar() {
        var ruta=document.frm_cliente;
        var $myForm = $('#frm-cliente');
        var el = $('#rut').parsley();
        var eRut = false;
        el.removeError('forcederror', {updateClass: true});
        $(el.ulError).empty();       
        if(!RutVar && $('#rut').val() !== '') {
          eRut = true;
          el.addError('forcederror', {message: 'el Rut es incorrecto'});
          $('#rut').focus();
          $('#rut').select();
        }
        if(eRut)
          return false;
        if(ruta.checkValidity()) {
          $('#modConfirma').modal({backdrop: "static"});
        } else {
          $('<input type="submit">').hide().appendTo($myForm).click().remove();		
        }
        return false;
      }      
    </script>     
  </body>
</html>