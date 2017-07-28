<?php
$css_form  = 1;
$css_table = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
//-- Verificar si hay accion por ejecutar
$acc = isset($_GET['acc'])?$_GET['acc']:'';
if($acc) {
  $token     = isset($_GET['token'])?$_GET['token']:'';
  $id        = isset($_GET['id'])?$_GET['id']:0;
  $des       = isset($_GET['descripcion'])?$_GET['descripcion']:'';
  $margen    = $_GET['margen'] ?? 0.00;
  $comision  = $_GET['comision'] ?? 0.00;
  $inicio    = $_GET['inicio'] ?? '';
  $fin       = $_GET['fin'] ?? '';
  $retorno   = $_GET['retorno'] ?? 0;
  $token_chk = md5($id.$des.$margen.$comision.$inicio.$fin.$retorno.'ajbc');
}

//-- Verificamos que hay accion y el token corresponde
if ($acc && $token != $token_chk) {
  unset($id, $token, $des, $acc, $token_chk);
  $str_error = "Accion rechazada, token incorrecto!";
}

//-- Buscar orden inicial para seleccion
$sql = "SELECT COUNT(*) AS cantidad FROM productos WHERE id>0";
$query = pg_query($conn, $sql);
$cantidad = 1;
if($fila = pg_fetch_assoc($query)) {
  $campaigns = $fila['cantidad'];
}
//$cantidad = ($etapas==0)?1:$etapas+1;
$cantidad = 10;
?>
        <!-- Contenido de página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left" style="width: 100%">
                <h3>Gestionar Campa&ntilde;as</h3>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <!-- Seleccion de campaña activa -->
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Campa&ntilde;a activa</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form id="frm-activa" data-parsley-validate class="form-horizontal form-label-left" name="frm_activa" method="post">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="activa">
                          Activar campa&ntilde;a seleccionada:
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <select id="activa" name="activa" class="form-control">
                            <option value="">Seleccione...</option>
<?php
$sql = "SELECT id, descripcion, estado FROM campaign WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  if($row['estado'] == 1) $sel = " selected";
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
              
              <!-- Formulario de Edicion o creacion de etapas -->
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Agregar/Editar</h2>
                    <ul class="nav navbar-right panel_toolbox">
<?php
if(isset($id) && $id != 0) {
?>
                      <li>
                        <a href="campaign.php" data-toggle="tooltip" data-placement="bottom" title="Agregar campaña">
                            <i class="fa fa-plus"></i>
                        </a>
                      </li>
<?php
}
?>
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="frm-campaign" data-parsley-validate class="form-horizontal form-label-left" name="frm_campaign" method="post">
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="descripcion">
                            Descripci&oacute;n <span class="required">*</span>
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="text" class="form-control"
                                 placeholder="Nombre de campaña"
                                 name="descripcion"
                                 id="descripcion"
<?php
if(isset($des) && $des) print "value=\"$des\"";
?>
                                 required="required">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="margen">
                            Margen máximo <span class="required">*</span>
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 placeholder="% descuento máximo"
                                 name="margen"
                                 id="margen"
<?php
if(isset($margen) && $margen) print "value=\"$margen\"";
?>                                 
                                 required="requiered"
                                 onkeypress="validate(event);"
                                 maxlength="5">
                          <span class="fa fa-percent form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="margen">
                            Comisi&oacute;n <span class="required">*</span>
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 placeholder="Margen de intermediación"
                                 name="comision"
                                 id="comision"
<?php
if(isset($comision) && $comision) print "value=\"$comision\"";
?>                                 
                                 required="requiered"
                                 onkeypress="validate2(event);"
                                 maxlength="5">
                          <span class="fa fa-percent form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>                        
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="inicio">
                          Inicio
                        </label>                        
                          <div class="control-group">
                            <div class="controls">
                              <div class="col-md-8 col-sm-8 col-xs-12 xdisplay_inputx form-group has-feedback">
                                <input type="text" class="form-control"
                                       id="inicio"
                                       name="inicio"
<?php
if(isset($inicio) && $inicio) print "value=\"$inicio\"";
?>                                       
                                       placeholder="dd/mm/yyyy"
                                       aria-describedby="inputSuccess2Status">
                                <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                                <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                              </div>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="fin">
                          Fin
                        </label>                        
                          <div class="control-group">
                            <div class="controls">
                              <div class="col-md-8 col-sm-8 col-xs-12 xdisplay_inputx form-group has-feedback">
                                <input type="text" class="form-control"
                                       id="fin" name="fin"
<?php
if(isset($fin) && $fin) print "value=\"$fin\"";
?>                                       
                                       placeholder="dd/mm/yyyy"
                                       aria-describedby="inputSuccess2Status">
                                <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                                <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                              </div>
                            </div>
                          </div>                          
                      </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="margen">
                            Campaña/retorno
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12 has-feedback">
                          <select id="retorno" name="retorno" class="form-control">
                            <option value="0">Seleccione...</option>
<?php
$sql = "SELECT id, descripcion, estado FROM campaign WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  if(isset($retorno) && $row['id'] == $retorno) $sel = " selected";
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
  $sel = "";
}
?>
                          </select>                            
                        </div>
                    </div>
                    
                      <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button class="btn btn-default" type="reset">Cancelar</button>
                                <button type="button" class="btn btn-success" onclick="validafrm();">Aceptar</button>
                            </div>
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
                                <button type="button" class="btn btn-success" name=save-frm" id="save-frm">Si</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!--/ Dialogo Modal de confirmacion -->
<?php
if(isset($id) && $id > 0) {
?>
                        <input type="hidden" name="id-campaign" id="id-campaign" value="<?php print $id ?>" />
<?php
}
?>
                    </form>
                  </div>
                </div>
              </div>
              
              <!-- Tabla de Campañas existentes -->
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Campañas existentes</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
<?php
if(isset($str_bien) || isset($str_error)) {
?>
                    <!-- tooltip de confirmacion -->
                    <div class="alert alert-<?php print isset($str_bien)?'success':'danger'; ?> alert-dismissible fade in" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                      </button>
                      <strong><?php print isset($str_bien)?'Exito':'Error' ?>!</strong>&nbsp;<?php print (isset($str_error)?$str_error:$str_bien); ?>
                    </div>
                    <!--/ tooltip de confirmacion -->
<?php
}
?>
                    <!-- tabla dinamica -->
                    <div id="historial" class="clearfix">
<?php
if($campaigns == 0) {
?>
                      <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action" id="tbl-campaign">
                          <thead>
                            <tr class="headings">
                              <th class="column-title" width="70%">Campa&ntildea</th>
                              <th class="column-title no-link last" colspan="2"><span class="nobr">Acci&oacute;n</span>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr class="even pointer">
                              <td class="text-center" colspan="4">Sin Registros para mostrar...</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
<?php
} else {
  include("ajax/tabla_campaign.php");
}
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
            <h4>Desea eliminar el Producto: <span id="str-etapa" style="color: red;">[etapa]</span> de la lista (S/N)?</h4>
            <small>(si el producto se encuentra relacionada a cualquier registro no podr&aacute; ser borrada)</small>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" id="cancela-borrar">No</button>
            <button type="button" class="btn btn-success" name="borrar-campaign" id="borrar-campaign">Si</button>
            </div>
          </div>
          </div>
          <form id="frm-elimina">
          </form>
        </div>
        <!--/ Dialogo Modal de Eliminacion -->
        
        <!-- /Contenido de página -->
<?php
include('includes/dash-footer.php');
pg_close($conn);
?>
<script type="text/javascript">
  //-- Validacion de Formulario y llamado a dialogo Modal
  function validafrm() {
    var ruta=document.frm_campaign;
    var $myForm = $('#frm-campaign');
    if(ruta.checkValidity()) {
      $('#modConfirma').modal({backdrop: "static"});
    } else {
      $('<input type="submit">').hide().appendTo($myForm).click().remove();		
    }
    return false;
  }  
</script>
<script>
  //-- Validar campo numerico
  function validate(evt) {
     var key = window.event ? evt.keyCode : evt.which;
     var valor = document.getElementById("margen").value;
     if (key === 8 || key === 46) {
      if(key === 46 && valor.indexOf('.') >= 0 ) {
        return false;
      }
      return true;
     } else if ( key < 48 || key > 57 ) {
       return false;
     } else {
       return true;
     }
  }
  function validate2(evt) {
     var key = window.event ? evt.keyCode : evt.which;
     var valor = document.getElementById("comision").value;
     if (key === 8 || key === 46) {
      if(key === 46 && valor.indexOf('.') >= 0 ) {
        return false;
      }
      return true;
     } else if ( key < 48 || key > 57 ) {
       return false;
     } else {
       return true;
     }
  }   
  function fn_elimina(id_etapa, des_etapa) {
    //-- Escribimos el texto en el modal
    $("#str-etapa").html(des_etapa);
    //-- creamos campo hidden para saber id a eliminar
    $("#id-eliminar").remove();
    $('<input>', {
        type : 'hidden',
        id   : 'id-eliminar',
        name : 'id-eliminar',
        value: id_etapa
      }).appendTo('#frm-elimina');    
    $('#modElimina').modal({backdrop: 'static'});
  }
  $('#cancela-borrar').click(function() {
    $("#id-eliminar").remove();
    $("#modElimina").modal('toggle');
  });
  $('#borrar-campaign').click(function() {
    $("#modElimina").modal('hide');
		$.ajax({type: 'POST',
			url: "ajax/tabla_campaign.php",
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
</script>
<script>
  $(document).ready(function() {
    $("[id='margen']").keypress(validate);
    $("[id='comision']").keypress(validate2);
    $('#inicio, #fin').daterangepicker({
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
  $("#save-frm").click(function() {
		$.ajax({ type: 'POST',
			url: "ajax/tabla_campaign.php",
			async: false,
			//-- Mostrar icono de espera mientras llega respuesta del script php
			beforeSend:
				function() {
          $('#modConfirma').modal('toggle');
					$.showLoading({name: 'jump-pulse',allowHide: false});			
				},
			data: {
					'id_campaign' : $('#id-campaign').val(),
					'descripcion' : $('#descripcion').val(),
          'margen'      : $('#margen').val(),
          'inicio'      : $('#inicio').val(),
          'fin'         : $('#fin').val(),
          'retorno'     : $('#retorno').val(),
          'comision'    : $('#comision').val()
				  },
			//-- Colocar respuesta del script php en el marco DIV indicado
			success:
				function(result) {
          $("#descripcion").val('').focus();
          //-- Borramos campo hidden antes de rellenar el nuevo html
          $("#id-campaign").remove();
					$("#historial").html(result);
					$.hideLoading();
					// bof inicializamos la tabla dinamica
					$("#tbl-campaign").dataTable({
					  "info"      	 : true,
					  "searching" 	 : false,
					  "ordering"     : false,
					  "lengthChange" : false,
					  language: {
						  paginate: {
							  first:    'Primera',
							  previous: 'Ant.',
							  next:     'Sig.',
							  last:     'Ultima'
							},
						  "info": "_START_ a _END_ de _TOTAL_",
						  "infoEmpty": "Sin datos para mostrar",
						  "emptyTable": "Sin datos para mostrar en tabla",
						  "lengthMenu": "Mostrar _MENU_ registros"
					  }              
					});				
					// eof inicializamos la tabla dinamica
				}
		});    
  });
</script>