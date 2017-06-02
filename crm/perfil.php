<?php
$css_form  = 1;
$notify    = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');

$frm_email  = $_POST['email'] ?? '';
$frm_movil  = $_POST['movil'] ?? '';
$frm_uname  = $_POST['uname'] ?? '';
$frm_clave  = $_POST['clave'] ?? '';
$frm_repite = $_POST['repite-clave'] ?? '';
if($frm_email) {
  $cc = '';
  if($frm_clave && $frm_clave == $frm_repite)
    $cc = ",clave=md5('$frm_clave') ";
  $sql = "UPDATE usuarios SET
          email = '$frm_email'
          ,movil = '$frm_movil'
          ,uname = '$frm_uname'
          $cc
          WHERE id=$idusuario";
  if( $query = pg_query($conn, $sql) )
    $updated = true;
  else
    $updated = false;
}


//-- Datos del perfil actual
$sql = "SELECT email, movil, uname, CONCAT(nombre, ' ', apellidos) AS nombre FROM usuarios WHERE id=$idusuario";
$query = pg_query($conn, $sql);
if($row = pg_fetch_assoc($query)) {
  $email = $row['email'];
  $movil = $row['movil'];
  $uname = $row['uname'];
  $nom   = $row['nombre'];
}
pg_close($conn);
?>
<!-- Contenido de la página -->
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Modificar perfil personal</h3>
        </div>
      </div>
      <div class="clearfix"></div>      
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2><?php print $nom ?></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <form id="frm-perfil" data-parsley-validate class="form-horizontal form-label-left" name="frm_cliente" method="post">
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                    Email
                  </label>
                  <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                    <input type="email" required=""
                           value="<?php print $email ?>"
                           class="form-control"
                           id="email"
                           name="email"
                           placeholder="Email de contacto">
                    <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span>
                  </div>                        
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="movil">
                    M&oacute;vil 
                  </label>
                  <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                    <input type="text" class="form-control"
                           id="movil"
                           name="movil"
                           placeholder="Movil" value="<?php print $movil ?>">
                    <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                  </div>
                </div>                
                <div class="form-group">
                   <label class="control-label col-md-2 col-sm-2 col-xs-12" for="uname">
                     Usuario
                   </label>
                   <div class="col-md-10 col-sm-10 col-xs-12">
                     <input type="text" class="form-control" placeholder="Nombre de usuario"
                            id="uname" name="uname" value="<?php print $uname ?>">
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
                            id="clave" name="clave">
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
                            id="repite-clave" name="repite-clave">
                   </div>                         
                 </div>                
                <!-- tabla dinamica -->
                <div id="historial" class="clearfix">
                </div>
                <!--/ tabla dinamica -->
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
                        <button type="submit" class="btn btn-success" name="btnGuardar" id="btnGuardar">Si</button>
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
<!-- /Contenido de la página -->
<?php
include('includes/dash-footer.php');
?>
<script>
<?php
if(isset($updated)) {
?>
  $(document).ready(function() {
<?php
if($updated) {
?>
	/* Notificacion exitosa */
	new PNotify({
		title  : 'Actualizacion',
		text   : 'Su perfil ha sido actualizado correctamente',
		type   : 'success',
		styling: 'bootstrap3'
	});
<?php
} else {
?>
	/* Notificacion de error */
	new PNotify({
		title  : 'Error!',
		text   : 'Ha ocurrido un error intentado actualizar su perfil, intente mas tarde o comuniquese con su administrador de sistemas',
		type   : 'error',
		styling: 'bootstrap3'
	});
<?php
}
?>
  });
<?php
}
?>
  /* Validar formulario de registro de Ejecutivos */
  function validar() {
    var ruta=document.frm_cliente;
    var $myForm = $('#frm-perfil');
    var cl = $('#repite-clave').parsley();

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
</script>