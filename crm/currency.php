<?php
$css_form  = 1;
$css_table = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
//--> BOF crear nueva moneda
$new_currency = isset($_POST['new_currency'])?$_POST['new_currency']:0;
$upd_currency = isset($_POST['upd_currency'])?$_POST['upd_currency']:0;
if($new_currency || $upd_currency) {
	$descripcion = $_POST['descripcion'];
	$simbolo	 = $_POST['simbolo'];
	$principal	 = (isset($_POST['principal']))?($_POST['principal']=='on')?1:0:0;
	$estado		 = (isset($_POST['activa']))?($_POST['activa']=='on')?1:0:0;
	if($new_currency) {
		$sql = "INSERT INTO monedas(descripcion, simbolo, principal, estado)
				SELECT '$descripcion', '$simbolo', $principal, $estado
				WHERE NOT EXISTS(SELECT id FROM monedas WHERE UPPER(simbolo)=UPPER('$simbolo'))
				RETURNING id";
				
	} elseif($upd_currency) {
		$id_moneda = $_POST['id_moneda'];
		$sql = "UPDATE monedas SET
					descripcion = '$descripcion'
					,simbolo = '$simbolo'
					,principal = '$principal'
					,estado = '$estado'
				WHERE id=$id_moneda
				RETURNING id";
				
	}
	$res = pg_query($conn, $sql);
	if(!$res)
		$str_error = "al intentar crear/actualizar el registro de moneda";
		
	if(!$registro = pg_fetch_assoc($res)) {
		$str_error = "Ya existe una moneda con el s&iacute;mbolo ($simbolo)";
	} else {
		$id_moneda = $registro['id'];
	}
	
	if(!isset($str_error))
		$str_bien = "Registro ($simbolo) actualizado correctamente!";
}
//<-- EOF crear nueva moneda

//--> BOF al seleccionar una moneda
$moneda = isset($_GET['moneda'])?$_GET['moneda']:0;
$token 	= isset($_GET['token'])?$_GET['token']:0;
if($moneda && $token == md5($moneda.'ajbc')) {
	$sql = "SELECT descripcion, simbolo, principal, estado
				  FROM monedas
					WHERE id=$moneda";
	$query = pg_query($conn, $sql);
	if($fila = pg_fetch_assoc($query)) {
		$descripcion = $fila['descripcion'];
		$simbolo	 = $fila['simbolo'];
		$principal	 = $fila['principal'];
		$estado		 = $fila['estado'];
		$id_moneda	 = $moneda;
	} else {
		$str_error = "Moneda no encontrada!";
	}
	
} else {
	$moneda    = 0;
	$token     = 0;
	unset($id_moneda);
}
//<-- EOF al seleccionar una moneda

//--> BOF Lista de monedas
$sql = "SELECT id, descripcion, simbolo
		FROM monedas
		ORDER BY (CASE WHEN principal=1 THEN 0 ELSE 1 END), descripcion";
$query = pg_query($conn, $sql);
//<-- EOF Lista de monedas
?>
<!-- Dropzone.js -->
<link rel="stylesheet" href="../css/dropzone.css">
<!-- Contenido de la página -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Gestionar Monedas</h3>
      </div>
    </div>

    <div class="clearfix"></div>
		<div class="row">
		 <!-- Primer Bloque, Seleccion de Moneda -->
		  <div class="col-md-6 col-xs-12">
			<div class="x_panel">
			  <div class="x_title">
				<h2>Monedas Existentes <small>Monedas registradas en el sistema</small></h2>
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
if($moneda && isset($str_error)) {
?>
			  <div class="alert alert-danger' alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
				<strong>Error!</strong>&nbsp;<?php print $str_error; ?>
			  </div>										
<?php
}
?>				
				<br />					
				<label class="control-label col-md-6 col-sm-6 col-xs-12 text-left" for="moneda">
					Elija Moneda para editar o ver detalles
				</label>										
				<ul class="nav nav-pills" role="tablist">
					<li role="presentation" class="dropdown col-md-6 col-sm-6 col-xs-12 text-center">
						<a id="drop4" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
						  <?php print ($moneda)?$descripcion." ($simbolo)":'Seleccione...'; ?>
						  <span class="caret"></span>
						</a>
						<ul id="menu6" class="dropdown-menu animated fadeInDown" role="menu">
<?php
while($fila = pg_fetch_assoc($query)) {
	$token_row = md5($fila['id'].'ajbc');
	print '<li role="presentation"><a role="menuitem" tabindex="-1" href="?moneda='.
		  $fila['id'].'&token='.$token_row.'">'.$fila['descripcion'].' ('.$fila['simbolo'].')'.
		  '</a></li/>';
}
?>													
						</ul>
					</li>
				</ul>				
			  </div>
			</div>

			<!-- Segundo Bloque, Formulario  -->
			<div class="x_panel">
				<div class="x_title">
					<h2>Crear o Editar Moneda</h2>
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
if(($new_currency || $upd_currency) && (isset($str_bien) || isset($str_error))) {
?>
			  <div class="alert alert-<?php print isset($str_bien)?'success':'danger'; ?> alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
				<strong><?php print isset($str_bien)?'Exito':'Error' ?>!</strong>&nbsp;<?php print (isset($str_error)?$str_error:$str_bien); ?>
			  </div>										
<?php
}
?>
				<br />
				<form id="frm-moneda" data-parsley-validate class="form-horizontal form-label-left" name="frm_moneda" method="post">
					<div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12" for="descripcion">
							Descripci&oacute;n <span class="required">*</span>
						</label>
						<div class="col-md-8 col-sm-8 col-xs-12">
							<input type="text" name="descripcion"
										 id="descripcion"
										 required="required"
										 class="form-control col-md-8 col-xs-12"
										 placeholder="Descripcion ej. Peso Chileno"
<?php
if($moneda && isset($descripcion)) {
	print " value=\"$descripcion\" ";
}
?>
										 maxlength="50">
						</div>
					</div>
										
					<div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12" for="simbolo">
							S&iacute;mbolo <span class="required">*</span>
						</label>
						<div class="col-md-8 col-sm-8 col-xs-12">
							<input type="text" name="simbolo"
										 id="simbolo"
										 required="required"
										 class="form-control col-md-8 col-xs-12"
										 placeholder="ej. CLP"
<?php
if($moneda && isset($descripcion)) {
	print " value=\"$simbolo\" ";
}
$chk_principal = (isset($principal) && $principal == 1)?'checked':'';
$chk_estado	   = (isset($estado) && $estado == 1)?'checked':'';
?>										 
										 maxlength="4">
						</div>
					</div>											
										
					<div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12">Moneda Principal</label>
						<div class="col-md-8 col-sm-8 col-xs-12">
							<div class="">
								<label>
									 <input type="checkbox" class="js-switch" <?php print $chk_principal ?> name="principal" />
								</label>
							</div>
						</div>
					 <label class="control-label col-md-4 col-sm-4 col-xs-12">Activa</label>
						<div class="col-md-8 col-sm-8 col-xs-12">
							<div class="">
								<label>
									 <input type="checkbox" class="js-switch" <?php print $chk_estado ?> name="activa" />
								</label>
							</div>
						</div>												
					</div>											
										
					<div class="ln_solid"></div>
					<div class="form-group">
						<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
							<button class="btn btn-default" type="reset">Cancelar</button>
							<!--<button type="submit" class="btn btn-success">Aceptar</button>-->													
							<!-- Boton que llama al dialogo Modal -->
							<!--
							<button type="button" class="btn btn-success"
											data-toggle="modal"
											data-target=".bs-example-modal-sm"
											onclick="return alert('hola');">Aceptar</button>
							-->
						  <button type="button" class="btn btn-success" onclick="validar();">Aceptar</button>
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
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
									<button type="submit" class="btn btn-success" name="submit1">Guardar</button>
								</div>
							</div>
						</div>
					</div>
					<!--/ Dialogo Modal de confirmacion -->
					<input type="hidden" name="<?php print isset($id_moneda)?'upd_currency':'new_currency' ?>" value="1" />
<?php
if(isset($id_moneda)) {
?>
					<input type="hidden" name="id_moneda" value="<?php print $id_moneda ?>" />
<?php
}
?>
					</form>
				</div>
			</div>
		  </div>
						
		  <!-- Tercer Bloque, Tipos de Cambio  -->
		  <div class="col-md-6 col-xs-12">
			<div class="x_panel">
			  <div class="x_title">
				<h2>Tipos de Cambio</h2>
				<ul class="nav navbar-right panel_toolbox">
				  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
				  </li>
				  <li><a class="close-link"><i class="fa fa-close"></i></a>
				  </li>
				</ul>
				<div class="clearfix"></div>
			  </div>
			  <div class="x_content">
				<br />
				<form id="frm-tasas" name="frm_tasas"
					  data-parsley-validate class="form-horizontal form-label-left"
					  method="post"
					  onsubmit="return false;">
				  <div class="form-group">
					<label class="control-label col-md-4 col-sm-4 col-xs-12" for="descripcion">
						Moneda/Cambio <span class="required">*</span>
					</label>
					<div class="col-md-8 col-sm-8 col-xs-12">
					  <select class="form-control" name="moneda_cambio" id="sel-cambio" required="required">
						<option value="">Seleccione...</option>
<?php
if($moneda) {
	pg_result_seek($query, 0);
	while($fila = pg_fetch_assoc($query)) {
		if($fila['id'] != $moneda)
			print "<option value=\"{$fila['id']}\">{$fila['descripcion']} ({$fila['simbolo']})</option>";
	}
}
?>						
					  </select> 
					</div>
				  </div>
										
				  <div class="form-group">
					<label class="control-label col-md-4 col-sm-4 col-xs-12" for="fecha">
						Fecha Inicio
					</label>
					<div class="col-md-8 col-sm-8 col-xs-9">
						 <input type="text" class="form-control" data-inputmask="'mask': '99/99/9999'"
								data-toggle="tooltip" data-placement="bottom" title="dd/mm/aaaa"
								id="fecha" name="fecha">
						 <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
			
				<div class="form-group">
				  <label class="control-label col-md-4 col-sm-4 col-xs-12" for="valor">
						Tipo de Cambio <span class="required">*</span>
				  </label> 
				  <div class="col-md-8 col-sm-8 col-xs-12">
					  <input type="text" name="valor"
							 id="valor"
							 required="required"
							 class="form-control col-md-8 col-xs-12"
							 placeholder="ej. 650.00"
							 maxlength="10"
							 onkeypress="validate(event);">
				  </div>
				</div>	
										
				<div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12 col-md-offset-3">
						<input type="hidden" name="frm_cambio" value="1">
						<button type="button" class="btn btn-dark" id="btn-csv"
								data-toggle="tooltip" data-placement="top"
								title="Click para cargar datos desde archivo (CSV). utilice punto y coma (;) como separador de campos"
								disabled="disabled">
							Subir CSV
						</button>
						<button class="btn btn-default" type="reset">Cancelar</button>
						<button type="button" onclick="validar2();" class="btn btn-success">Aceptar</button>
					</div>
				</div>
					<!-- Dialogo Modal de confirmacion -->
					<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="modConfirma2">
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
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
									<button type="button" class="btn btn-success" name="submit2" id="btnGuardar">Guardar</button>
								</div>
							</div>
						</div>
					</div>
					<!--/ Dialogo Modal de confirmacion -->										
				</form>
				
				<!-- Objeto dropzone -->
				<div class="ln_solid" id="sep-csv" style="display: none;"></div>
				<div class="form-group" id="div-csv" style="display: none;">
				  <form action="csv_currency.php" class="dropzone" id="myDropzone">
					<div class="fallback">
					  <input name="file" type="file" multiple />
					  <input type="submit" value="Procesar archivo">
					</div>
<?php
if (isset($id_moneda)) {
?>
					<input type="hidden" name="idmoneda" value="<?php print $id_moneda ?>">
<?php
}
?>
				  </form>					
				</div>
				<!--/ Objeto dropzone -->
				
				<div class="ln_solid"></div>
				<!-- tabla dinamica -->
				<div id="historial" class="clearfix">
                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Fecha  </th>
                            <th class="column-title">Tipo/Cambio </th>
                            <th class="column-title no-link last"><span class="nobr">Acci&oacute;n</span>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr class="even pointer">
                            <td class="text-center" colspan="3">Seleccione una moneda...</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
				</div>
				<!--/ tabla dinamica --> 
			  </div>
			</div>
		  </div>
						
		</div>

</div>
</div>

	<!-- Dialogo de informacion General -->
	<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true" id="modalGeneral">
		<div class="modal-dialog">
			<div class="modal-content">
		
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
					</button>
					<h4 class="modal-title" id="myModalLabel3">CSV procesado...</h4>
				</div>
				<div class="modal-body">
					<p>El Archivo ha sido procesado, haga click en [Aceptar] para refrescar la tabla!</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="btnModalGeneral">Aceptar</button>
				</div>
			</div>
		</div>
	</div>
	<!--/ Dialogo Modal de confirmacion -->
	
<!--/ Contenido de la página -->
<?php
include('includes/dash-footer.php');
pg_close($conn);
?>
<!-- Dropzone.js -->
<script src="../js/dropzone.js"></script>
<script>
$(function() {
  // Configuraciones y control de objeto dropzone
  Dropzone.options.myDropzone = {
	paramName: "file",
	maxFiles: 1,
	maxFilesize: 5,
	acceptedFiles: "text/csv",
	dictDefaultMessage: "Has click o arrastra tu archivo (CSV) a este recuadro",
	dictFallbackMessage: "Tu navegador no soporta arrastrar y subir archivos!",
	dictInvalidFileType: "Tipo de archivo incorrecto. Debe ser formato CSV",
	dictFileTooBig: "Archivo muy grande ({{filesize}}), maximo permitido: {{maxFilesize}}",
	dictMaxFilesExceeded: "No puede subir mas de 1 archivo!",
	createImageThumbnails: false,
	accept: function(file, done) {
	  if (file.type != "text/csv") {
		done("Se espera archivo separado por coma (*.csv)!");
	  }
	  else { done(); }
	}
  };
  var myDropzone = new Dropzone("#myDropzone");
  myDropzone.on("success", function(file) {
	//alert("Archivo agregado!");
	$('#modalGeneral').modal({backdrop: "static"});
  });     
});
</script>
<!--/ Dropzone.js -->
<script type="text/javascript">
function validar() {
	var ruta=document.frm_moneda;
	//if(ruta.descripcion.value !== "" && ruta.simbolo.value !== "") {
	if(ruta.checkValidity()) {
		$('#modConfirma').modal({backdrop: "static"});
	} else {
		ruta.submit1.click();
	}
	return false;
}

function validar2() {
	var ruta=document.frm_tasas;
	var $myForm = $('#frm-tasas');
	if(ruta.checkValidity()) {
		$('#modConfirma2').modal({backdrop: "static"});
	} else {
		$('<input type="submit">').hide().appendTo($myForm).click().remove();		
	}
	return false;
}

//-- Validar campo numerico
function validate(evt) {
	 var key = window.event ? evt.keyCode : evt.which;
	 var valor = document.getElementById("valor").value;
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
</script>

<script>
$(document).ready(function(){
    $("#sel-cambio").change(function(){
		$.ajax({type: 'POST',
			url: "ajax/tabla_monedas.php",
			async: false,
			//-- Mostrar icono de espera mientras llega respuesta del script php
			beforeSend:
				function() {
					$.showLoading({name: 'jump-pulse',allowHide: false});
					//-- Agregar campo oculto para tener el ID
					$("#idmoneda_valor").remove();
					$('<input>', {
						type : 'hidden',
						id   : 'idmoneda_valor',
						name : 'idmoneda_valor',
						value: $('#sel-cambio').val()
					}).appendTo('#myDropzone');					
				},
			data: {
					'id_moneda': '<?php print isset($id_moneda)?$id_moneda:0 ?>',
					'id_valor': this.value
				  },
			//-- Colocar respuesta del script php en el marco DIV indicado
			success:
				function(result){
					$("#historial").html(result);
					$.hideLoading();
					$("#btn-csv").prop('disabled', false);
					// bof inicializamos la tabla dinamica
					$("#tbl-moneda").dataTable({
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
	
	$("#btnGuardar").click(function() {
		$("#modConfirma2").modal('hide');
		$.ajax({type: 'POST',
			url: "ajax/guarda_valor.php",
			async: false,
			//-- Mostrar splash de espera mientras llega respuesta del script php
			beforeSend:
				function() {
					$.showLoading({name: 'jump-pulse',allowHide: false});
				},
			data: {
					'id_moneda': '<?php print isset($id_moneda)?$id_moneda:0 ?>',
					'id_valor': document.getElementById("sel-cambio").value,
					'fecha': document.getElementById("fecha").value,
					'valor': document.getElementById("valor").value
				  },
			//-- Colocar respuesta del script php en el marco DIV indicado
			success:
				function(result){
					$("#historial").html(result);
					$.hideLoading();
				}
		});
	});
	//-- Validar numeros en campo de valor
	$("[id^=valor]").keypress(validate);
	
	//-- Mostrar dropzone
	$("#btn-csv").click(function() {
		$("#sep-csv").toggle();
		$("#div-csv").toggle();
	});
	
	//-- Actualizar tabla de valores
	$("#btnModalGeneral").click(function() {
		$("#sel-cambio").change();	
	});
	
});
</script>
