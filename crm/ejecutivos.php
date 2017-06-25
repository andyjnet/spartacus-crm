<?php
$css_form  = 1;
$css_table = 1;
$notify    = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
?>
        <script>var RutVar=false, SalvarNuevo = 0;</script>
        <!-- Contenido de la página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Gestion de Ejecutivos</h3>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" id="div-frm" style="display: none;">
                  <div class="x_title">
                    <h2>Datos generales y contactos <small>Formulario de actualizacion de datos de ejecutivos</small></h2>
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
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="rut">
                          RUT <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <input type="text"
                                 class="form-control"
                                 style="text-transform:uppercase;"
                                 id="rut" placeholder="RUT"
                                 maxlength="12"
                                 required="required"
                                 data-parsley-error-message="el Rut es requerido para continuar">
                        </div>                         
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="ingreso">
                          Fecha/ingreso <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback" id="radios">
                          <input type="text" class="form-control" data-inputmask="'mask': '99/99/9999'"
                             data-toggle="tooltip" data-placement="bottom" title="dd/mm/aaaa"
                             id="ingreso" name="ingreso" required="required">
                          <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
                        </div>                       
                      </div>                   
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">
                          Nombres <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Indique ambos nombres" id="nombre" required="required">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="apellidos">
                          Apellidos <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Apellido paterno y materno" id="apellidos" required="required">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          Tel&eacute;fono 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="telefono" placeholder="Telefono fijo">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          M&oacute;vil 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="movil" placeholder="Movil">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          Email
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                          <input type="email" data-parsley-trigger="change" class="form-control" id="email" placeholder="Email de contacto">
                          <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span>
                        </div>                        
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="direccion">
                            Direcci&oacuten
                          </label>
                          <div class="col-md-10 col-sm-10 col-xs-12">
                            <textarea id="direccion" class="form-control" name="direccion" rows="2" placeholder="Direccion de contacto"></textarea>
                          </div>
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="cargo">
                          Cargo <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="cargo" class="form-control" required>
                            <option value="0" es-sup="0">Seleccione...</option>
<?php
//-- Cargos
$sql = "SELECT id, descripcion, supervisado FROM cargos WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\" es-sup=\"{$row['supervisado']}\">{$row['descripcion']}</option>";
}
?>
                          </select>                          
                        </div>  
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="nivel">
                          Categoria/Nivel <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="nivel" class="form-control" required>
                            <option value="0">Seleccione...</option>
<?php
//-- Categorias o Niveles de Ejecutivos
$sql = "SELECT id, descripcion FROM niveles WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\">{$row['descripcion']}</option>";
}
?>
                          </select>  
                        </div>                        
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="supervisor">
                          Supervisor
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="supervisor" class="form-control" required>
                            <option value="0">Seleccione...</option>
<?php
//-- Supervisor inmediato
$sql = "SELECT id, CONCAT(nombre, ' ',apellidos) AS descripcion
        FROM usuarios
        WHERE id>0
          AND estado = 1
        ORDER BY nombre, apellidos";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\">{$row['descripcion']}</option>";
}
?>
                          </select>  
                        </div>                         
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="sucursal">
                          Sucursal <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="sucursal" class="form-control" required>
                            <option value="0">Seleccione...</option>
<?php
//-- Cargos
$sql = "SELECT id, descripcion FROM sucursales WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\">{$row['descripcion']}</option>";
}
?>
                          </select>                          
                        </div>                         
                      </div>                       
                      <br />
                      <h2>Datos de Acceso</h2>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="chk-usuario">
                          Usuario Activo
                        </label>                        
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <div class="">
                            <label>
                              <input type="checkbox" class="js-switch" id="chk-usuario" checked />
                            </label>
                          </div>
                        </div>                       
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="uname">
                          Usuario
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control" placeholder="Nombre de usuario"
                                 id="uname">
                        </div>                         
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="clave">
                          Clave
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="password" class="form-control"
                                 placeholder="Contraseña"
                                 data-parsley-trigger="change"
                                 id="clave">
                        </div>                       
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="repite-clave">
                          Repita clave
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="password" class="form-control"
                                 placeholder="Repita contraseña"
                                 data-validate-linked="clave"
                                 data-parsley-trigger="change"
                                 id="repite-clave">
                        </div>                         
                      </div>
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
                      <input type="hidden" id="pwd-usr" value="">
                      <input type="hidden" id="idejecutivo" value="0">
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Listado de ejecutivos <small>lista de ejecutivos actuales</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a data-toggle="tooltip" data-placement="bottom" title="Agregar Cliente" id="new-client">Nuevo</a>
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
$ejecutivos = 1;
include("ajax/tabla_ejecutivos.php");
unset($ejecutivos);
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
<script>
  $(document).ready(function() {
    $("#rut").keypress(validate);
    /* controlar comportamiento segun tipo de cliente */
    $('#tipoJ').on('ifChecked', function () {
       /* $('#nombre-contacto').show(); */
       $('#nombre-fantasia').show();
    });
    $('#tipoN').on('ifChecked', function () { 
       /* $('#nombre-contacto').hide(); */
       $('#nombre-fantasia').hide();
    });
    $('#rut').Rut({
      on_error  : function() { RutVar = false; },
      on_success: function() { RutVar = true; }
    });
    $("#new-client").click(function() {
      $("#div-frm").toggle();
    });
    $("#cierra-frm").click(function() {
      $("#new-client").click();
    });
    $("#resetFrm").click(function() {
      $("#cargo").children().attr('selected', false);
      $("#nivel").children().attr('selected', false);
      $("#sucursal").children().attr('selected', false);
      $("#cargo").val('0');
      $("#nivel").val('0');
      $("#sucursal").val('0');      
      $('#frm-cliente').parsley().reset();
      $("div").removeClass("checked");
      if(SalvarNuevo === 0)
        $("#new-client").click();
      $("html, body").animate({ scrollTop: 0 }, "slow");
    });
    $("#btnGuardar").click(function() {
      var esUsuario = $('input[id="chk-usuario"]:checked').length > 0;
      var usrActivo = 0;
      if(esUsuario)
         usrActivo = 1;
      $("#modConfirma").modal('hide');
      $.ajax({type: 'POST',
        url: "ajax/tabla_ejecutivos.php",
        async: false,
        /* Mostrar splash de espera mientras llega respuesta del script php */
        beforeSend:
          function() {
            $.showLoading({name: 'jump-pulse',allowHide: false});
          },
        data: {
            'rut'        : document.getElementById("rut").value,
            'ingreso'    : document.getElementById("ingreso").value,
            'nombre'     : document.getElementById("nombre").value,
            'apellidos'  : document.getElementById("apellidos").value,
            'telefono'   : document.getElementById("telefono").value,
            'movil'      : document.getElementById("movil").value,
            'email'      : document.getElementById("email").value,
            'direccion'  : document.getElementById("direccion").value,
            'cargo'      : document.getElementById("cargo").value,
            'nivel'      : document.getElementById("nivel").value,
            'sucursal'   : document.getElementById("sucursal").value,
            'usuario'    : usrActivo,
            'uname'      : document.getElementById("uname").value,
            'clave'      : document.getElementById("clave").value,
            'pwd'        : document.getElementById("pwd-usr").value,
            'idejecutivo': document.getElementById("idejecutivo").value,
            'supervisor' : document.getElementById("supervisor").value
            },
        /* Colocar respuesta del script php en el marco DIV indicado */
        success:
          function(result){
            $("#historial").html(result);
            if(result.indexOf('No hay ejecutivos') < 0)
              activatbl("#tbl-ejecutivos");
            $.hideLoading();
            if(SalvarNuevo == 1) {
              SalvarNuevo = 0;
              $( "#rut" ).focus();
              $("html, body").animate({ scrollTop: 0 }, "slow");
            }
          }
      });
    });
    
    $("#chk-usuario").on("click", function() {
      /*var esUsuario = $('input[id="chk-usuario"]:checked').length > 0;*/
      var esUsuario = $(this.checked).length > 0;
      $("#uname").prop('disabled', !esUsuario);
      $("#clave").prop('disabled', !esUsuario);
      $("#repite-clave").prop('disabled', !esUsuario);
      
    });
    $('#borrar-fase').click(function() {
      $("#modElimina").modal('hide');
      $.ajax({type: 'POST',
        url: "ajax/tabla_ejecutivos.php",
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
<?php
if( !isset($NoActivar) || $NoActivar == false ) {
?>    
    /* Activamos la tabla dinamica una vez que haya cargado la pagina */
    activatbl("#tbl-ejecutivos");
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
      "bPaginate"    : false,
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
  /* Validar formulario de registro de Ejecutivos */
  function validar(paramGuardarNuevo) {
    var ruta=document.frm_cliente;
    var $myForm = $('#frm-cliente');
    var esSup = $( "#cargo option:selected" ).attr("es-sup");
    var el = $('#rut').parsley();
    var cl = $('#repite-clave').parsley();
    var es = $('#supervisor').parsley();
    var eRut = false;
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
    /* Verificar que el supervisor sea requerido */
    es.removeError('forcederror', {updateClass: true});
    $(es.ulError).empty();
    if(esSup == "1" && $('#supervisor').val() == "0") {
      es.addError('forcederror', {message: 'El cargo seleccionado requiere supervisor'});
      $("#supervisor").focus();
      return false;
    }
    
    /* Verificamos la contraseña */
    cl.removeError('forcederror', {updateClass: true});
    $(cl.ulError).empty();
    if($('#clave').val() !== "" && $('#clave').val() !== $('#repite-clave').val()) {
      cl.addError('forcederror', {message: 'Las contraseñas no coinciden'});
      return false;
    }
    /* Validacion automatica */
    if(ruta.checkValidity()) {
      $('#modConfirma').modal({backdrop: "static"});
    } else {
      $('<input type="submit">').hide().appendTo($myForm).click().remove();		
    }
    return false;
  }
  function fn_elimina(id_fase, des_fase) {
    /* Escribimos el texto en el modal */
    $("#str-fase").html(des_fase);
    /* creamos campo hidden para saber id a eliminar */
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
          'objeto': 'ejecutivo',
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
</script>