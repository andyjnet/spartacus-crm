<?php
$css_form  = 1;
$css_table = 1;
$notify    = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
/* parametros get */
$cid = isset($_GET['cid'])?$_GET['cid']:'';
if($cid) 
  $cid = base64_decode($cid);

/* Estados/Etapas de Cotizaion */
$sql = "SELECT id, descripcion,
          (CASE adjunto WHEN true THEN 'si' ELSE 'no' END) AS adjunto
        FROM etapas_venta
        WHERE id>0 AND estado=1
        ORDER BY orden";
$query = pg_query($conn, $sql);
?>
        <!-- fileuploader -->
        <link href="../css/jquery.fileuploader.css" media="all" rel="stylesheet">
        <script>var RutVar=false, SalvarNuevo = 0;</script>     
        <!-- Contenido de la página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Gestion de cotizaciones y polizas</h3>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" id="div-frm" style="display: none;">
                  <div class="x_title">
                    <h2>Datos cliente y contacto</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="" id="cierra-frm"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form id="frm-cliente" data-parsley-validate class="form-horizontal form-label-left"
                          name="frm_cliente"
                          method="post"
                          enctype="multipart/form-data">
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="rut">
                          Cliente
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <input type="text"
                                 class="form-control"
                                 id="rut" placeholder="RUT"
                                 maxlength="12"
                                 readonly="readonly">
                        </div> 
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text"
                                 class="form-control"
                                 id="cliente"
                                 placeholder="Nombre o razon social"
                                 maxlength="100"
                                 readonly="readonly">
                        </div>                        
                      </div>                   
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nom-fantasia">
                          Nombre de Fantasia
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Nombre de fantasia" id="nom-fantasia" readonly="readonly">
                        </div>
                      </div>
                      <div class="form-group" id="nombre-fantasia">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nom-contacto">
                          Contacto
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Persona de contacto" id="nom-contacto">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          Tel&eacute;fono <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="telefono" placeholder="Telefono fijo">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="movil">
                          M&oacute;vil <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="movil" placeholder="Movil">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">
                          Email <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                          <input type="email" data-parsley-trigger="change" class="form-control" id="email" placeholder="Email de contacto">
                          <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span>
                        </div>                        
                      </div>
                      <br />
                      <h2>Detalles de Poliza</h2>
                      <div class="ln_solid"></div>

                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="etapa">
                          Estado/Etapa <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <select id="etapa" class="form-control" required>
                            <option value="" is-attach="no">Seleccione...</option>
<?php
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\" is-attach=\"{$row['adjunto']}\"$sel>{$row['descripcion']}</option>";
}
/* Sucursales */
$sql = "SELECT id, descripcion FROM sucursales WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
?>
                          </select>                          
                        </div>                         
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="sucursal">
                          Sucursal <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="sucursal" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
/* Corredores */
$sql = "SELECT id, descripcion FROM corredores WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
?>
                          </select>                          
                        </div>  
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="corredor">
                          Corredor <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="corredor" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
?>
                          </select>  
                        </div>                        
                      </div>                     
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="ejecutivo">
                          Ejecutivo <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="ejecutivo" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
$sql = "SELECT id, CONCAT(nombre, ' ', apellidos) AS descripcion FROM usuarios WHERE estado=1 ORDER BY nombre";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
/* Ramos */
$sql = "SELECT id, descripcion, vehiculo FROM ramos WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
?>
                          </select>                          
                        </div>  
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="ramo">
                          Ramo <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="ramo" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}:{$row['vehiculo']}\"$sel>{$row['descripcion']}</option>";
}
?>
                          </select>  
                        </div>                        
                      </div>
                      <div id="vehiculo" style="display: none">
                        <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="patente">
                            Patente <span class="required">*</span>
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="patente" placeholder="Nro de patente de vehiculo">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="marca">
                            Marca <span class="required">*</span>
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="marca" placeholder="Marca">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                        </div>                        
                        <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="modelo">
                            Modelo <span class="required">*</span>
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="modelo" placeholder="Modelo de vehiculo">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="year">
                            A&ntilde;o <span class="required">*</span>
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="year" placeholder="Año de vehiculo">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                        </div>  
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="siniestros">
                          Siniestros 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="siniestros" placeholder="Monto siniestros causados">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="prima">
                          Prima actual 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="prima" placeholder="Monto prima actual">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="prima-neta">
                          Prima Neta 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="prima-neta" placeholder="Monto prima neta">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="monto-asegurado">
                          Monto Asegurado 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="monto-asegurado" placeholder="Monto asegurado">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="vigencia">
                          Vigencia
                        </label>                        
                          <div class="control-group">
                            <div class="controls">
                              <div class="col-md-4 col-sm-4 col-xs-12 xdisplay_inputx form-group has-feedback">
                                <input type="text" class="form-control" id="vigencia" placeholder="Vigencia" aria-describedby="inputSuccess2Status">
                                <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                                <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                              </div>
                            </div>
                          </div>
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="renovacion">
                          Renovaci&oacute;n
                        </label>                        
                          <div class="control-group">
                            <div class="controls">
                              <div class="col-md-4 col-sm-4 col-xs-12 xdisplay_inputx form-group has-feedback">
                                <input type="text" class="form-control" id="renovacion" placeholder="Renovacion" aria-describedby="inputSuccess2Status">
                                <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                                <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                              </div>
                            </div>
                          </div>                          
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="observacion">
                            Observaciones
                          </label>
                          <div class="col-md-10 col-sm-10 col-xs-12">
                            <textarea id="observacion" class="form-control" name="comentarios" rows="2" placeholder="Ingrese comentarios o notas relacionadas a la cotización o poliza"></textarea>
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="poliza" style="color: red;">
                          N&ordm; de poliza 
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="poliza" placeholder="Numero de poliza">
                          <span class="fa fa-shield form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <!--
                      <div id="poliza-adjunto" style="display: none;">
                        <h1 style="text-align: center;">subir archivo!</h1>                      
                      </div>
                      -->
                      <br />
                      <h2>Archivos adjuntos</h2>
                      <div class="ln_solid"></div>
                      <input type="file" name="files" id="adjunto-file">                      
                      
                      <div class="ln_solid"></div>
                      <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                        <button type="reset" class="btn btn-default" id="resetFrm">Cancelar</button>
                        <button type="button" class="btn btn-default" onclick="validar('1');">Guardar & Nuevo</button>
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
                      <input type="hidden" id="idcotizacion" value="0">
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Lista de cotizaciones <small>Resumen de cotizaciones registradas</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a data-toggle="tooltip" data-placement="bottom" title="Importaci&oacute;n masiva" id="import-client">Importar</a>
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <!-- tabla dinamica -->
                    <div id="historial" class="clearfix">
<?php
$clientes = 1;
include("ajax/tabla_cotizacion.php");
unset($clientes);
?>
                    </div>
                    <!--/ tabla dinamica --> 
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Dialogo Modal de Eliminacion -->
        <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="modElimina">
          <div class="modal-dialog">
          <div class="modal-content">
        
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            <h4 class="modal-title" id="myModalLblElimina">Confirme...</h4>
            </div>
            <div class="modal-body">
            <h4>Desea eliminar el registro: <span id="str-fase" style="color: red;">[fase]</span> de la lista (S/N)?</h4>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" id="cancela-borrar">No</button>
            <button type="button" class="btn btn-success" name="borrar-fase" id="borrar-fase">Si</button>
            </div>
          </div>
          </div>
          <form id="frm-elimina">
          </form>
        </div>
        <!--/ Dialogo Modal de Eliminacion -->
        
        <!--/ Contenido de la página -->
<?php
include('includes/dash-footer.php');
@pg_close($conn);
?>
<!-- Validacion de RUT Chileno -->
<script src="../js/jquery.Rut.js" type="text/javascript"></script>
<!-- fileuploader -->
<script src="../js/jquery.fileuploader.min.js" type="text/javascript"></script>
<script>
  $(document).ready(function() {
    $("#rut").keypress(validate);
    $('#rut').Rut({
      on_error  : function() { RutVar = false; },
      on_success: function() { RutVar = true; }
    });
    $("#new-client").click(function() {
      $("#div-frm").toggle();
      if($("#div-frm").is(":hidden"))
        $("html, body").animate({ scrollTop: 0 }, "slow");
    });
    $("#cierra-frm").click(function() {
      $("#new-client").click();
    });
    $("#resetFrm").click(function() {
      $("#ejecutivo").children().attr('selected', false);
      $("#estado").children().attr('selected', false);
      $("#ejecutivo").val('0');
      $("#estado").val('0');       
      $('#frm-cliente').parsley().reset();
      $("div").removeClass("checked");
      $("#vehiculo").hide();
      if(SalvarNuevo === 0)
        $("#new-client").click();
    });
    $("#btnGuardar").click(function() {
      $("#modConfirma").modal('hide');
      $.ajax({type: 'POST',
        url: "ajax/tabla_cotizacion.php",
        async: false,
        /* Mostrar splash de espera mientras llega respuesta del script php */
        beforeSend:
          function() {
            $.showLoading({name: 'jump-pulse',allowHide: false});
          },
        data: {
            'tipo'        : $("#frm-cliente input[type='radio']:checked").val(),
            'rut'         : document.getElementById("rut").value,
            'nombre'      : document.getElementById("nombre").value,
            'fantasia'    : document.getElementById("nom_fantasia").value,
            'ejecutivo'   : document.getElementById("ejecutivo").value,
            'comentarios' : document.getElementById("comentarios").value,
            'estado'      : document.getElementById("estado").value,
            'contacto'    : document.getElementById("contacto").value,
            'telefono'    : document.getElementById("telefono").value,
            'movil'       : document.getElementById("movil").value,
            'email'       : document.getElementById("email").value,
            'direccion'   : document.getElementById("direccion").value,
            'idcliente'   : document.getElementById("idcliente").value
            },
        /* Colocar respuesta del script php en el marco DIV indicado */
        success:
          function(result){
            $("#historial").html(result);
            if(result.indexOf('No hay cotizaciones') < 0)
              activatbl("#tbl-clientes");
            $.hideLoading();
            if(SalvarNuevo == 1) {
              SalvarNuevo = 0;
              $( "#rut" ).focus();
              $("html, body").animate({ scrollTop: 0 }, "slow");
            }
          }
      });
    });
    $('#borrar-fase').click(function() {
      $("#modElimina").modal('hide');
      $.ajax({type: 'POST',
        url: "ajax/tabla_cotizacion.php",
        async: false,
        //-- Mostrar icono de espera mientras llega respuesta del script php
        beforeSend:
          function() {
            $.showLoading({name: 'jump-pulse',allowHide: false});			
          },
        data: {
            'id-eliminar': $('#id-eliminar').val()
          },
        //-- Colocar respuesta del script php en el marco DIV indicado
        success:
          function(result){
            $("#historial").html(result);
            $.hideLoading();
          }
      });     
    });    
    $('#cancela-borrar').click(function() {
      $("#id-eliminar").remove();
      $("#modElimina").modal('toggle');
    });
    $('#ramo').on("change", function() {
      var esVehiculo = ($(this).val().split(":")[1] === '1');
      if(esVehiculo)
        $("#vehiculo").show();
      else
        $("#vehiculo").hide();
    });
    $('#vigencia, #renovacion').daterangepicker({
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
    /*
    $("#poliza").on("change keyup paste mouseup", function() {
      if($(this).val().length >= 5) {
        $("#poliza-adjunto").show();
      } else {
        $("#poliza-adjunto").hide();
      }
    });
    */
    
    /*** opciones de fileuploader ***/
		var input = $('input[name="files"]').fileuploader({
      enableApi: true, 
			limit	  : 1,
			extensions: ['jpg', 'jpeg', 'png', 'pdf'],
			captions: {
					button: function(options) { return 'Seleccionar ' + (options.limit == 1 ? 'Archivo' : 'Archivos'); },
					feedback: function(options) { return 'Seleccione ' + (options.limit == 1 ? 'archivo' : 'archivos') + ' a subir'; },
					feedback2: function(options) { return options.length + ' ' + (options.length > 1 ? ' archivos fueron' : ' archivo fue') + ' seleccionado'; },
					drop: 'Arrastre los archivos aqui para subirlos',
					paste: '<div class="fileuploader-pending-loader"><div class="left-half" style="animation-duration: ${ms}s"></div><div class="spinner" style="animation-duration: ${ms}s"></div><div class="right-half" style="animation-duration: ${ms}s"></div></div> Pegando un archivo, click aqui para cancelar.',
					removeConfirmation: 'Esta seguro que desea borrar este archivo?',
					errors: {
						filesLimit: 'Solo ${limit} archivos pueden ser subidos.',
						filesType: 'solo se permiten archivos de tipo ${extensions}.',
						fileSize: '${name} es muy grande! Por favor seleccione un archivo no mayor de ${fileMaxSize}MB.',
						filesSizeAll: 'Los archivos que has seleccionado son muy grandes! solo puede subir archivos hasta ${maxSize} MB.',
						fileName: 'Archivo con el nombre ${name} ya ha sido seleccionado',
						folderUpload: 'No esta permitido subir carpeetas.'
					}
				}			
		});
		window.api = $.fileuploader.getInstance(input);    
<?php
if( !isset($NoActivar) || $NoActivar == false ) {
?>    
    /* Activamos la tabla dinamica una vez que haya cargado la pagina */
    activatbl("#tbl-clientes");
<?php
}
?>
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
  /* Funcion para activar Tabla dinamica */
  function activatbl(id_tabla) {
    $(id_tabla).dataTable({
      "info"      	 : true,
      "searching" 	 : true,
      "ordering"     : true,
      "lengthChange" : true,
      language: {
        paginate: {
          first:    'Primera',
          previous: 'Ant.',
          next:     'Sig.',
          last:     'Ultima'
        },
        "info"         : "_START_ a _END_ de _TOTAL_",
        "infoEmpty"    : "Sin datos para mostrar",
        "emptyTable"   : "Sin datos para mostrar en tabla",
        "lengthMenu"   : "Mostrar _MENU_ registros",
        "search"       : "Buscar:",
        "zeroRecords"  : "Ning&uacute;n registro coincide con su b&uacute;squeda!",
        "infoFiltered" :  "(filtrados de _MAX_ registros totales)"
      }              
    });				
  }
  /* Validar formulario de registro de Clientes */
  function validar(paramGuardarNuevo) {
    var ruta=document.frm_cliente;
    var $myForm = $('#frm-cliente');
    var el = $('#rut').parsley();
    var eRut = false;
    var eFile = false;
    var el2 = $("#adjunto-file").parsley();
    if (typeof paramGuardarNuevo === 'undefined') paramGuardarNuevo = 0;
    SalvarNuevo = paramGuardarNuevo;
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
    if(api.getFiles().length === 0) {
      if($("#poliza").val().length >= 5)
        eFile = true;
      if($("#etapa").children(":selected").attr("is-attach") == 'si')
        eFile = true;
    }
    el2.removeError('forcederror', {updateClass: true});
    $(el2.ulError).empty();      
    if(eFile) {
      el2.addError('forcederror', {message: 'Debe adjuntar un archivo para continuar'});
      return false;
    }    
    if(ruta.checkValidity()) {
      $('#modConfirma').modal({backdrop: "static"});
    } else {
      $('<input type="submit">').hide().appendTo($myForm).click().remove();		
    }
    return false;
  }
  function fn_elimina(id_fase, des_fase) {
    //-- Escribimos el texto en el modal
    $("#str-fase").html(des_fase);
    //-- creamos campo hidden para saber id a eliminar
    $("#id-eliminar").remove();
    $('<input>', {
        type : 'hidden',
        id   : 'id-eliminar',
        name : 'id-eliminar',
        value: id_fase
      }).appendTo('#frm-elimina');    
    $('#modElimina').modal({backdrop: 'static'});
  }
  function fn_modifica(id) {
    $.ajax({type: 'POST',
      url: "ajax/traeob.php",
      async: false,
      //-- Mostrar icono de espera mientras llega respuesta del script php
      beforeSend:
        function() {
          $.showLoading({name: 'jump-pulse',allowHide: false});			
        },
      data: {
          'objeto': 'cliente',
          'id'    : id
        },
      //-- Colocar respuesta del script php en el marco DIV indicado
      success:
        function(result){
          $("#a-editar"+id).tooltip('hide');
          $("#div-frm").show();
          $("head").append(result);
          $.hideLoading();
        }
    });     
  }
<?php
if($cid) {
?>
  $("#div-frm").show();
<?php
}
?>
</script>