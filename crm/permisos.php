<?php
$css_form  = 1;
$notify    = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
?>
<link rel='stylesheet' href='../css/jquery.bonsai.css'> 
<!-- Contenido de la página -->
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Gestionar permisos de usuario</h3>
        </div>
      </div>
      <div class="clearfix"></div>      
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Permisos seg&uacute;n perfil</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <form id="frm-permisos" data-parsley-validate class="form-horizontal form-label-left" name="frm_permisos" method="post">
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="cargo">
                    Elija Perfil
                  </label>
                  <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                    <select id="cargo" name="cargo" class="form-control" required>
                      <option value="">Seleccione...</option>
<?php
//-- Buscar perfiles existentes
$sql = "SELECT id, descripcion FROM cargos WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
?>
                      <option value="<?php print $row['id'] ?>"><?php print $row['descripcion'] ?></option>
<?php
}
?>
                    </select>
                  </div>                        
                </div>
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="checkboxes">
                    Defina Permisos
                  </label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <!-- TreeView colapsable -->
                    <ol id='checkboxes'>
                      <li class='expanded'><input id="chkmenu00" type='checkbox' value='root' /> Todos los permisos
                        <ol>
                            <li>
                                <input id="chkmenu0100" type='checkbox' value='0' /> Clientes
                                <ol>
                                  <li><input id="chkmenu0101" type="checkbox" value='2' /> Ver todos (objetos propios y creados por otros perfiles)</li>
                                  <li><input id="chkmenu0102" type="checkbox" value='3' /> Crear</li>
                                  <li><input id="chkmenu0103" type="checkbox" value='4' /> Modificar</li>
                                  <li><input id="chkmenu0104" type="checkbox" value='5' /> Eliminar</li>
                                </ol>
                            </li>
                            <li>
                                <input id="chkmenu0200" type='checkbox' value='-1' /> Cotizaciones
                                <ol>
                                  <li><input id="chkmenu0201" type="checkbox" value='-2' /> Ver todas (objetos propios y credos por otros perfiles)
                                    <ol>
<?php
$sql="SELECT id, descripcion FROM etapas_venta WHERE estado=1 ORDER BY orden";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
    $id  = $row['id'] + 1000;
    $des = $row['descripcion'];
    print "<li><input id='chkmenu0202-$id' type='checkbox' value='$id' /> $des</li>";
}
?>
                                    </ol>
                                  </li>
                                  <li><input id="chkmenu0202" type="checkbox" value='8' /> Crear</li>
                                  <li><input id="chkmenu0203" type="checkbox" value='9' /> Modificar</li>
                                  <li><input id="chkmenu0204" type="checkbox" value='10' /> Eliminar</li>
                                  <li><input id="chkmenu0204" type="checkbox" value='11' /> Adjuntar documentos</li>
                                </ol>
                            </li>
                            <li>
                                <input id="chkmenu0300" type='checkbox' value='-3' /> Tablas de Valores
                                <ol>
                                  <li><input id="chkmenu0301" type="checkbox" value='13' /> Cargos</li>
                                  <li><input id="chkmenu0302" type="checkbox" value='14' /> Categorias/Ejecutivos</li>
                                  <li><input id="chkmenu0350" type="checkbox" value='26' /> Compa&ntilde;ias de seguros</li>
                                  <li><input id="chkmenu0303" type="checkbox" value='15' /> Corredores</li>
                                  <li><input id="chkmenu0304" type="checkbox" value='16' /> Embudo de Ventas</li>
                                  <li><input id="chkmenu0305" type="checkbox" value='17' /> Fases de Clientes</li>
                                  <li><input id="chkmenu0349" type="checkbox" value='25' /> Formas de pago</li>
                                  <li><input id="chkmenu0351" type="checkbox" value='27' /> Gestionar Campa&ntilde;a</li>                                  
                                  <li><input id="chkmenu0306" type="checkbox" value='18' /> Monedas</li>
                                  <li><input id="chkmenu0348" type="checkbox" value='24' /> Productos</li>
                                  <li><input id="chkmenu0307" type="checkbox" value='19' /> Ramos</li>
                                  <li><input id="chkmenu0308" type="checkbox" value='20' /> Sucursales</li>
                                </ol>
                            </li>
                            <li>
                                <input id="chkmenu0400" type='checkbox' value='-4' /> Usuarios
                                <ol>
                                  <li><input id="chkmenu0401" type="checkbox" value='22' /> Gestionar</li>
                                  <li><input id="chkmenu0402" type="checkbox" value='23' /> Permisos</li>
                                </ol>
                            </li>                            
                        </ol>
                      </li>
                    </ol>
                    <!--/ TreeView colapsable -->
                  </div>
                </div>          
                <!-- tabla dinamica -->
                <div id="historial" class="clearfix"></div>
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
<!-- /Contenido de la página -->
<?php
include('includes/dash-footer.php');
?>
<!-- Libreria bonsai para treeview con checkboxes -->
<script src='../js/jquery.bonsai.js'></script>
<script src='../js/jquery.qubit.js'></script>  
<script>
  $(document).ready(function() {
    $('#checkboxes').bonsai({
      expandAll: true,
      checkboxes: true, 
      handleDuplicateCheckboxes: true,
      addSelectAll: true
    });
    $("#cargo").on("change", function() {
        var idCargo = $("#cargo").val();
        $.ajax({type: 'POST',
            url: "ajax/perfiles.php",
            async      : false,       
          /* Mostrar splash de espera mientras llega respuesta del script php */
          beforeSend: 
            function() {
              $.showLoading({name: 'jump-pulse',allowHide: false});
              $(".none").click();
            },
          data: {
                'idcargo' : idCargo,
                'accion'  : 'buscar'
            },
          success:
            function(result){
                $("#historial").html(result);
                $.hideLoading();
                $( "#cargo" ).focus();
                $("html, body").animate({ scrollTop: 0 }, "slow");
            },
          error:
            function(result){
                $.hideLoading();
                alert("Error de Ajax: "+result);
            }
        });        
    });
    $("#btnGuardar").click(function() {
        var idCargo = $("#cargo").val();
        var perfil = $("#cargo option:selected").text();
        var items = $('[id*="chkmenu"]:checked').map(function() { return $(this).val().toString(); } ).get().join(",");
        $("#modConfirma").modal('hide');
      
        $.ajax({type: 'POST',
          url: "ajax/perfiles.php",
          async: false,
          /* Mostrar splash de espera mientras llega respuesta del script php */
          beforeSend:
            function() {
              $.showLoading({name: 'jump-pulse',allowHide: false});
            },
          data: {
                  'idcargo' : idCargo,
                  'items'   : items,
                  'accion'  : 'guardar',
                  'perfil'  : perfil
              },
          /* Colocar respuesta del script php en el marco DIV indicado */
          success:
            function(result){
                $("#historial").html(result);
                $.hideLoading();
                $( "#cargo" ).focus();
                $("html, body").animate({ scrollTop: 0 }, "slow");
            },
          error:
            function(result){
                $.hideLoading();
                alert("Error de Ajax: "+result);
            }
        });
    });    
<?php
if(isset($updated) && $updated) {
?>
    /* Notificacion exitosa */
    new PNotify({
      title  : 'Actualizacion',
      text   : 'Los permisos se han actualizado correctamente',
      type   : 'success',
      styling: 'bootstrap3'
    });
<?php
} elseif(isset($updated) && !$updated) {
?>
    /* Notificacion de error */
    new PNotify({
      title  : 'Error!',
      text   : 'Ha ocurrido un error intentado actualizar los permisos, intente mas tarde o comuniquese con su administrador de sistemas',
      type   : 'error',
      styling: 'bootstrap3'
    });
<?php
}
?>
  });
  /* Validar formulario de registro de Ejecutivos */
  function validar() {
    var ruta=document.frm_permisos;
    var $myForm = $('#frm-permisos');

    /* Validacion automatica */
    if(ruta.checkValidity()) {
      $('#modConfirma').modal({backdrop: "static"});
    } else {
      $('<input type="submit">').hide().appendTo($myForm).click().remove();		
    }
    return false;
  } 
</script>