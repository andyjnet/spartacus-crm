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
  $token_chk = md5($id.'ajbc');
}

//-- Verificamos que hay accion y el token corresponde
if ($acc && $token != $token_chk) {
  unset($id, $token, $des);
  $str_error = "Accion rechazada, token incorrecto!";
}
//-- Buscar orden inicial para seleccion
$sql = "SELECT COUNT(*) AS cantidad FROM cargos";
$query = pg_query($conn, $sql);
$cargos = 0;
if($fila = pg_fetch_assoc($query)) {
  $cargos = $fila['cantidad'];
}
?>
        <!-- Contenido de página -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left" style="width: 100%">
                <h3>Gestionar cargos ejecutivos</h3>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              
              <!-- Formulario de Edicion o creacion de cargos -->
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Agregar/Editar</h2>
                    <ul class="nav navbar-right panel_toolbox">
<?php
if(isset($id) && $id != 0) {
?>
                      <li>
                        <a href="sucursales.php" data-toggle="tooltip" data-placement="bottom" title="Nueva Sucursal">
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
                    <form id="frm-fases-cliente" data-parsley-validate class="form-horizontal form-label-left" name="frm_fases_cliente" method="post">
                      <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="descripcion">
                            Descripci&oacute;n <span class="required">*</span>
                        </label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                          <input type="text" class="form-control"
                                 placeholder="nombre de Cargo"
                                 name="descripcion" id="descripcion"
<?php
if(isset($des) && $des != '') {
  print " value=\"$des\" ";
}
?>
                                 required="required">
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
                        <input type="hidden" name="id-cargo" id="id-cargo" value="<?php print $id ?>" />
<?php
}
?>
                    </form>
                  </div>
                </div>
              </div>
              
              <!-- Tabla de Sucursales -->
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Cargos registrados</h2>
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
if($cargos == 0) {
?>
                      <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action" id="tbl-fases">
                          <thead>
                            <tr class="headings">
                              <th class="column-title" width="80%">Descripci&oacute;n</th>
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
  include("ajax/tabla_cargos.php");
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
        
        <!-- /Contenido de página -->
<?php
include('includes/dash-footer.php');
pg_close($conn);
?>
<script type="text/javascript">
  //-- Validacion de Formulario y llamado a dialogo Modal
  function validafrm() {
    var ruta=document.frm_fases_cliente;
    var $myForm = $('#frm-fases-cliente');
    if(ruta.checkValidity()) {
      $('#modConfirma').modal({backdrop: "static"});
    } else {
      $('<input type="submit">').hide().appendTo($myForm).click().remove();		
    }
    return false;
  }  
</script>
<script>
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
  $('#cancela-borrar').click(function() {
    $("#id-eliminar").remove();
    $("#modElimina").modal('toggle');
  });
  $('#borrar-fase').click(function() {
    $("#modElimina").modal('hide');
		$.ajax({type: 'POST',
			url: "ajax/tabla_cargos.php",
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
		$.ajax({type: 'POST',
			url: "ajax/tabla_cargos.php",
			async: false,
			//-- Mostrar icono de espera mientras llega respuesta del script php
			beforeSend:
				function() {
          $('#modConfirma').modal('toggle');
					$.showLoading({name: 'jump-pulse',allowHide: false});			
				},
			data: {
					'id_cargo': $('#id-cargo').val(),
					'descripcion': $('#descripcion').val()
				  },
			//-- Colocar respuesta del script php en el marco DIV indicado
			success:
				function(result){
          $("#descripcion").val('').focus();
          $("#id-cargo").remove();
					$("#historial").html(result);
					$.hideLoading();
					// bof inicializamos la tabla dinamica
					$("#tbl-fases").dataTable({
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