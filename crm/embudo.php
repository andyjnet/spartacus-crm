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
  $orden     = isset($_GET['orden'])?$_GET['orden']:1;
  $adjunto   = isset($_GET['adjunto'])?$_GET['adjunto']:'f';
  $masivo    = isset($_GET['masivo'])?$_GET['masivo']:'f';
  $poliza    = isset($_GET['poliza'])?$_GET['poliza']:'f';
  $estado    = isset($_GET['estado'])?$_GET['estado']:1;
  $des       = isset($_GET['descripcion'])?$_GET['descripcion']:'';
  $token_chk = md5($id.'ajbc');
}

//-- Verificamos que hay accion y el token corresponde
if ($acc && $token != $token_chk) {
  unset($id, $token, $orden, $adjunto, $masivo, $estado, $des, $acc, $token_chk);
  $str_error = "Accion rechazada, token incorrecto!";
}

//-- Buscar orden inicial para seleccion
$sql = "SELECT COUNT(*) AS cantidad FROM etapas_venta";
$query = pg_query($conn, $sql);
$cantidad = 1;
if($fila = pg_fetch_assoc($query)) {
  $etapas = $fila['cantidad'];
}
//$cantidad = ($etapas==0)?1:$etapas+1;
$cantidad = 10;
?>
        <!-- Contenido de página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left" style="width: 100%">
                <h3>Etapas del embudo de Ventas</h3>
              </div>
            </div>

            <div class="clearfix"></div>

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
                        <a href="embudo.php" data-toggle="tooltip" data-placement="bottom" title="Agregar Etapa">
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
                    <form id="frm-embudo" data-parsley-validate class="form-horizontal form-label-left" name="frm_embudo" method="post">
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="descripcion">
                            Descripci&oacute;n <span class="required">*</span>
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="text" class="form-control"
                                 placeholder="nombre de la etapa"
                                 name="descripcion" id="descripcion"
<?php
if(isset($des) && $des != '') {
  print " value=\"$des\" ";
}
?>
                                 required="required">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="sel-orden">
                            Orden <span class="required">*</span>
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <select class="form-control" id="sel-orden" name="sel-orden" required="required"
                                  data-toggle="tooltip" data-placement="bottom" title="Seleccione el orden segun el flujo de su empresa">
                            <option value="">Seleccione</option>
<?php
$selected = "";
for($x=1; $x<=$cantidad; $x++) {
  $selected = (isset($orden) && $orden == $x)?"selected":"";
  print "<option value=\"$x\" $selected>$x</option>";
}
//-- Valores siguientes (checks)
$checked_adjunto = (isset($adjunto) && $adjunto == 't')?"checked":"";
$checked_masivo  = (isset($masivo) && $masivo == 't')?"checked":"";
$checked_poliza  = (isset($poliza) && $poliza == 't')?"checked":"";
$checked_estado  = (isset($estado) && $estado == 'f')?"":"checked";
?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="chk-adjunto"
                               data-toggle="tooltip" data-placement="top" title="La etapa requiere adjuntar documento">
                            Requiere adjunto
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="checkbox" class="js-switch" name="chk-adjunto" id="chk-adjunto" <?php print $checked_adjunto ?> /> 
                        </div>
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="chk-masivo"
                               data-toggle="tooltip" data-placement="top" title="Etapa por defecto para cargas masivas">
                            Asignacion Masiva
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="checkbox" class="js-switch" name="chk-masivo" id="chk-masivo" <?php print $checked_masivo ?> /> 
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="chk-poliza"
                               data-toggle="tooltip" data-placement="top" title="Etapa por defecto para cargas masivas">
                            Requiere Poliza
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="checkbox" class="js-switch" name="chk-poliza" id="chk-poliza" <?php print $checked_poliza ?> /> 
                        </div>
                      </div>                      
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="chk-estado"
                                data-toggle="tooltip" data-placement="bottom" title="Etapa Activa">
                            Activa
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="checkbox" class="js-switch" name="chk-estado" id="chk-estado" <?php print $checked_estado ?> /> 
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
                        <input type="hidden" name="id-etapa" id="id-etapa" value="<?php print $id ?>" />
<?php
}
?>
                    </form>
                  </div>
                </div>
              </div>
              
              <!-- Tabla de Etapas o niveles de embudo de Ventas -->
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Etapas</h2>
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
if($etapas == 0) {
?>
                      <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action" id="tbl-etapas">
                          <thead>
                            <tr class="headings">
                              <th class="column-title" width="10%">Orden</th>
                              <th class="column-title" width="70%">Etapa</th>
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
  include("ajax/tabla_etapas.php");
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
            <h4>Desea eliminar la etapa: <span id="str-etapa" style="color: red;">[etapa]</span> de la lista (S/N)?</h4>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" id="cancela-borrar">No</button>
            <button type="button" class="btn btn-success" name="borrar-etapa" id="borrar-etapa">Si</button>
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
    var ruta=document.frm_embudo;
    var $myForm = $('#frm-embudo');
    if(ruta.checkValidity()) {
      $('#modConfirma').modal({backdrop: "static"});
    } else {
      $('<input type="submit">').hide().appendTo($myForm).click().remove();		
    }
    return false;
  }  
</script>
<script>
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
  $('#borrar-etapa').click(function() {
    $("#modElimina").modal('hide');
		$.ajax({type: 'POST',
			url: "ajax/tabla_etapas.php",
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
  $("#save-frm").click(function() {
    var chkAdj = 'off', chkMas = 'off', chkAct = 'on', chkPoliza = 'off';
    if ($('#chk-adjunto').is(":checked")) {
      chkAdj = 'on';
    }
    if ($('#chk-masivo').is(":checked")) {
      chkMas = 'on';
    }
    if ($('#chk-poliza').is(":checked")) {
      chkPoliza = 'on';
    }    
    if(!$('#chk-estado').is(":checked")) {
      chkAct = 'off';
    }
		$.ajax({type: 'POST',
			url: "ajax/tabla_etapas.php",
			async: false,
			//-- Mostrar icono de espera mientras llega respuesta del script php
			beforeSend:
				function() {
          $('#modConfirma').modal('toggle');
					$.showLoading({name: 'jump-pulse',allowHide: false});			
				},
			data: {
					'id_etapa'   : $('#id-etapa').val(),
					'descripcion': $('#descripcion').val(),
          'orden'      : $('#sel-orden').val(),
          'adjunto'    : chkAdj,
          'masivo'     : chkMas,
          'estado'     : chkAct,
          'poliza'     : chkPoliza
				  },
			//-- Colocar respuesta del script php en el marco DIV indicado
			success:
				function(result){
          //-- Valores por defecto en el formulario
          $("#sel-orden").val('');
          if(chkAdj == 'on') $('#chk-adjunto').click();
          if(chkMas == 'on') $('#chk-masivo').click();
          if(chkPoliza == 'on') $('#chk-poliza').click();
          if(chkAct == 'off') $('#chk-estado').click();
          $("#descripcion").val('').focus();
          //-- Borramos campo hidden antes de rellenar el nuevo html
          $("#id-etapa").remove();
					$("#historial").html(result);
					$.hideLoading();
					// bof inicializamos la tabla dinamica
					$("#tbl-etapas").dataTable({
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