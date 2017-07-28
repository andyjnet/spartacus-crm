<?php
$css_form  = 1;
$css_table = 1;
$notify    = 1;
include('includes/dash-header.php');
include('../includes/funciones.php');
include('../includes/conn.php');
include_once('../includes/tools.php');
$id_sup_usr = $_SESSION['supervisor'];
/* parametros get */
$cid = isset($_GET['cid'])?$_GET['cid']:'';
if(!$cid = base64_decode($cid, true)) $cid = '';
 /* Datos del usuario */
 $comision_usuario = 0;
 if($idusuario) {
    $sql = "SELECT comision FROM usuarios WHERE id=$idusuario";
    $query = pg_query($conn, $sql);
    $row = pg_fetch_assoc($query);
    $comision_usuario = $row['comision'];
 }
 
/* Buscar los datos del cliente */
if($cid) {
  $sql = "SELECT c.rut, c.nombre, c.nombre_fantasia,
            cc.nombre AS contacto,
            cc.telefono, cc.movil, cc.email
          FROM clientes c
            LEFT JOIN clientes_contactos cc ON(cc.idcliente=c.id)
          WHERE c.id = $cid";
  $query = pg_query($conn, $sql);
  if($cliente = pg_fetch_assoc($query)) {
    $rut      = $cliente['rut'];
    $nombre   = $cliente['nombre'];
    $fantasia = $cliente['nombre_fantasia'];
    $contacto = $cliente['contacto'];
    $telefono = $cliente['telefono'];
    $movil    = $cliente['movil'];
    $email    = $cliente['email'];
  }
}
?>
      <style type="text/css">
        .dataTables_processing {
          position: absolute;
          top: 50%;
          left: 50%;
          width: 250px;
          height: 50px;
          margin-left: -125px;
          margin-top: -15px;
          padding: 14px 0 2px 0;
          border: 1px solid #ddd;
          text-align: center;
          color: white;
          font-size: 15px;
          background-color: #73879C;
        }			
      </style>
        <!-- fileuploader -->
        <link href="../css/jquery.fileuploader.css" media="all" rel="stylesheet">
        <script>var SalvarNuevo = 0;</script>     
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
                                 id="rut" name="rut"
                                 placeholder="RUT"
                                 maxlength="12"
                                 value="<?php print isset($rut)?$rut:'' ?>"
                                 readonly="readonly">
                        </div> 
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text"
                                 class="form-control"
                                 id="cliente" name="cliente"
                                 placeholder="Nombre o razon social"
                                 value="<?php print isset($nombre)?$nombre:'' ?>"
                                 readonly="readonly">
                        </div>                        
                      </div>                   
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nom-fantasia">
                          Nombre de Fantasia
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text"
                                 class="form-control"
                                 placeholder="Nombre de fantasia"
                                 id="nom-fantasia" name="nom-fantasia"
                                 value="<?php print isset($fantasia)?$fantasia:'' ?>"
                                 readonly="readonly">
                        </div>
                      </div>
                      <div class="form-group" id="nombre-fantasia">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nom-contacto">
                          Contacto
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <input type="text" class="form-control"
                                 placeholder="Persona de contacto"
                                 value="<?php print isset($contacto)?$contacto:'' ?>"
                                 id="nom-contacto" name="nom-contacto">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="telefono">
                          Tel&eacute;fono 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 id="telefono" name="telefono"
                                 value="<?php print isset($telefono)?$telefono:'' ?>"
                                 placeholder="Telefono fijo">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="movil">
                          M&oacute;vil 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 id="movil" name="movil"
                                 value="<?php print isset($movil)?$movil:'' ?>"
                                 placeholder="Movil">
                          <span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">
                          Email 
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                          <input type="email" data-parsley-trigger="change"
                                 class="form-control"
                                 id="email" name="email"
                                 value="<?php print isset($email)?$email:'' ?>"
                                 placeholder="Email de contacto">
                          <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span>
                        </div>                        
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="direccion">
                          Direcci&oacute;n
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 id="direccion" name="direccion"
                                 value="<?php print isset($direccion)?$direccion:'' ?>"
                                 placeholder="Calle número, departamento">
                          <span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="region">
                          Regi&oacute;n 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 id="region" name="region"
                                 value="<?php print isset($region)?$region:''; ?>"
                                 placeholder="Indique region">
                          <span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>                          
<!-- Select automatico                          
                          <select id="region" name="region" class="form-control">
                            <option value="0">Seleccione...</option>
<?php
/* Regiones  */
$sql = "SELECT id_re AS id,
          CONCAT(str_descripcion,' (', str_romano, ')') AS descripcion
        FROM region_cl
        WHERE id_re>0
        ORDER BY id_re";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
?>                            
                          </select>
-->
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="provincia">
                          Provincia
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 id="provincia" name="provincia"
                                 value="<?php print isset($provincia)?$provincia:'' ?>"
                                 placeholder="Indique Provincial">
                          <span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
<!-- Select automatico                          
                          <select id="provincia" name="provincia" class="form-control">
                            <option value="0">Seleccione una region</option>
                          </select>
-->
                        </div>
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="comuna">
                          Comuna 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control"
                                 id="comuna" name="comuna"
                                 value="<?php print isset($comuna)?$comuna:'' ?>"
                                 placeholder="Indique comuna o ciudad">
                          <span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>                          
<!-- Select automatico
                          <select id="comuna" name="comuna" class="form-control">
                            <option value="0">Seleccione una provincia</option>                           
                          </select>
-->                          
                        </div>
                      </div>
                      
                      <br />
                      <h2>Status</h2>
                      <div class="ln_solid"></div>

                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="etapa">
                          Estado/Etapa <span class="required">*</span>
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <select id="etapa" name="etapa" class="form-control" required>
                            <option value="" is-attach="no" is-poliza="no">Seleccione...</option>
<?php
/* Estados/Etapas de Cotizacion  */
$sql = "SELECT id, descripcion,
          (CASE adjunto WHEN true THEN 'si' ELSE 'no' END) AS adjunto,
          (CASE poliza  WHEN true THEN 'si' ELSE 'no' END) AS poliza
        FROM etapas_venta
        WHERE id>0 AND estado=1
        ORDER BY orden";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\" is-attach=\"{$row['adjunto']}\" is-poliza=\"{$row['poliza']}\"$sel>{$row['descripcion']}</option>";
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
                          <select id="sucursal" name="sucursal" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
/* Ejecutivos */
$sql = "SELECT id, CONCAT(nombre, ' ', apellidos) AS descripcion
        FROM usuarios
        WHERE estado=1
          AND idcargo > 0
        ORDER BY nombre, apellidos";
$query = pg_query($conn, $sql);
?>
                          </select>                          
                        </div>  
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="ejecutivo">
                          Ejecutivo <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="ejecutivo" name="ejecutivo" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  if(!strpos($sel,"selected")) {
    if($cid && $row['id'] == $idusuario)
      $sel = " selected";
  }  
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
  $sel = "";
}
?>
                          </select>  
                        </div>                        
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="vigencia">
                          Vigencia
                        </label>                        
                          <div class="control-group">
                            <div class="controls">
                              <div class="col-md-4 col-sm-4 col-xs-12 xdisplay_inputx form-group has-feedback">
                                <input type="text" class="form-control" id="vigencia" name="vigencia" placeholder="Vigencia" aria-describedby="inputSuccess2Status">
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
                                <input type="text" class="form-control" id="renovacion" name="renovacion" placeholder="Renovacion" aria-describedby="inputSuccess2Status">
                                <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                                <span id="inputSuccess2Status" class="sr-only">(Exito)</span>
                              </div>
                            </div>
                          </div>                          
                      </div>
                      
                      <br />
                      <h2>Producto</h2>
                      <div class="ln_solid"></div>
                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12 text-left" for="ramo">
                          Ramo <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="ramo" name="ramo" class="form-control" required>
                            <option value="">Seleccione...</option>
<?php
/* Ramo */
$sql = "SELECT id, descripcion, vehiculo FROM ramos WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}:{$row['vehiculo']}\"$sel>{$row['descripcion']}</option>";
}
?>
                          </select>  
                        </div>                         
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="producto">
                          Producto <span class="required">*</span>
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                          <select id="producto" name="producto" class="form-control">
                            <option value="0">Seleccione...</option>
<?php
$sql = "SELECT id, descripcion FROM productos WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
?>
                          </select>                          
                        </div>  
                       
                      </div>
                      <div id="vehiculo" style="display: none">
                        <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="patente">
                            Patente 
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="patente" name="patente" placeholder="Nro de patente de vehiculo">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="marca">
                            Marca 
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="marca" name="marca" placeholder="Marca">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                        </div>                        
                        <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="modelo">
                            Modelo 
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Modelo de vehiculo">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="year">
                            A&ntilde;o 
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="year" name="year" placeholder="Año de vehiculo">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="chasis">
                            Chasis
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="chasis" name="chasis" placeholder="Número de chasis">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="motor">
                            Motor 
                          </label>
                          <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="motor" name="motor" placeholder="Número de motor">
                            <span class="fa fa-car form-control-feedback right" aria-hidden="true"></span>
                          </div>
                        </div>                         
                      </div>
                      <div id="div-condominio" style="display: none">
                        <div class="form-group">
                          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="condominio">
                            Condominio
                          </label>
                          <div class="col-md-10 col-sm-10 col-xs-12 has-feedback">
                            <input type="text" class="form-control" id="condominio" name="condominio" placeholder="Número/Código de condominio">
                            <span class="fa fa-building form-control-feedback right" aria-hidden="true"></span>
                          </div>
                        </div>                            
                      </div>
                      
                      <br />
                      <h2>Asegurabilidad</h2>
                      <div class="ln_solid"></div>
                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="monto-asegurado">
                          Monto Asegurado 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="monto-asegurado" name="monto-asegurado"
                                 placeholder="Monto asegurado"
                                 onkeypress="validate_monto(event);">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="prima-neta">
                          Prima Neta 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="prima-neta" name="prima-neta"
                                 placeholder="Monto prima neta"
                                 onkeypress="validate_neta(event);"
                                 onkeyup="$('#prima-neta').trigger('change');">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>                        
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="prima">
                          Prima Bruta 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="prima" name="prima"
                                 placeholder="Monto prima actual"
                                 onkeypress="validate_actual(event);"
                                 onkeyup="$('#comision').trigger('change');">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="cuota">
                          Valor Cuota
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="cuota" name="cuota"
                                 placeholder="Monto de la cuota">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>

                      <br />
                      <h2>Condiciones</h2>
                      <div class="ln_solid"></div>
                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="forma-pago">
                          Forma de pago
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <select id="forma-pago" name="forma-pago" class="form-control">
                            <option value="0">Seleccione...</option>
<?php
$sql = "SELECT id, descripcion FROM formas_pago WHERE id>0 ORDER BY descripcion";
$query = pg_query($conn, $sql);
$sel = (pg_numrows($query) == 1)?" selected":"";
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\"$sel>{$row['descripcion']}</option>";
}
/* Bloquear campo si el usuario tiene un supervisor */
$str_bloqueo = '';
if($id_sup_usr)
  $str_bloqueo = 'readonly="readonly"';
?>
                          </select>   
                        </div>
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="deducible">
                          Deducible
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="deducible" name="deducible"
                                 placeholder="UF monto deducible">
                          <span class="fa fa-dollar form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      
                      <br />
                      <h2>Remuneraci&oacute;n</h2>
                      <div class="ln_solid"></div>
                      
                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="comision">
                          Comisi&oacute;n (%) 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="comision" name="comision"
                                 placeholder="% de comision"
                                 <?php print $str_bloqueo ?>
                                 data-toggle="tooltip" data-placement="bottom"
                                 title="Porcentaje de comisi&oacute;n"
                                 onkeypress="validate(event);"
                                 onkeyup="$('#comision').trigger('change');">
                          <span class="fa fa-percent form-control-feedback right" aria-hidden="true"></span>
                        </div>
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="comision">
                          Comisi&oacute;n Spartacus 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="comision-corredor" name="comision-corredor"
                                 placeholder="Monto de comisión Spartacus"
                                 readonly="readonly"
                                 data-toggle="tooltip" data-placement="bottom"
                                 title="Monto de comisi&oacute;n Corredor Spartacus">
                          <span class="fa fa-money form-control-feedback right" aria-hidden="true"></span>
                        </div>                          
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="comision-valor">
                          Comisi&oacute;n ($) 
                        </label>
                        <div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
                          <input type="text" class="form-control" id="comision-valor" name="comision-valor"
                                 placeholder="Monto ($) comisión"
                                 readonly = "readonly"
                                data-toggle="tooltip" data-placement="bottom"
                                 title="Valor de comisi&oacute;n en moneda"
                                 onkeypress="validate(event);">
                          <span class="fa fa-money form-control-feedback right" aria-hidden="true"></span>
                        </div>                                                 
                      </div>                      
                      
                      <br />
                      <h2>Otros</h2>
                      <div class="ln_solid"></div>

                      <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="cia">
                          Compa&ntilde;ia 
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                          <select id="cia" name="cia" class="form-control">
                            <option value="0">Seleccione...</option>
<?php
/* Estados/Etapas de Cotizacion  */
$sql = "SELECT id, descripcion
        FROM companias
        WHERE id>0 
        ORDER BY descripcion";
$query = pg_query($conn, $sql);
while($row = pg_fetch_assoc($query)) {
  print "<option value=\"{$row['id']}\">{$row['descripcion']}</option>";
}
?>
                          </select>                          
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
                          <input type="text" class="form-control" id="poliza"
                                 name="poliza" placeholder="Numero de poliza">
                          <span class="fa fa-shield form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
<?php
if($usr_admin == 1 || comprueba($usr_permisos, "11")) {
?>
                      <br />
                      <h2>Archivos adjuntos</h2>
                      <div class="ln_solid"></div>
                      <input type="file" name="files" id="adjunto-file">                      
                      <div id="tabla-adjuntos"></div>
<?php
}
?>
          
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
                      <input type="hidden" id="idcotizacion" name="idcotizacion" value="0">
                      <input type="hidden" id="comision-usuario" name="comision-usuario" value="<?php print $comision_usuario ?>">
<?php
if($cid) {
?>
                      <input type="hidden" id="idcliente" name="idcliente" value="<?php print $cid ?>">
<?php
}
?>
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
                      <li><a data-toggle="tooltip" data-placement="bottom"
                             title="Importaci&oacute;n masiva"
                             href="importar-cot.php"
                             id="import-cot">Importar
                          </a>
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action" id="tbl-cotizaciones" width="100%">
                        <thead>
                          <tr class="headings">
                          <th class="column-title text-left">ID</th>
                          <th class="column-title text-left">Cliente</th>
                          <th class="column-title text-left">Telefono </th>
                          <th class="column-title text-left">Detalles </th>
                          <th class="column-title text-left">Etapa </th>
                          <th class="column-title text-left">Monto </th>
                          <th class="column-title text-left">Vigencia </th>
                          <th class="column-title text-left">Ejecutivo </th>
                          <th class="column-title no-link last text-center"><span class="nobr">Acci&oacute;n</span>
                          </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr class="even pointer">
                            <td class="text-center" colspan="9">Cargando datos del Servidor...</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>   
                    <!-- tabla dinamica -->
                    <div id="historial" class="clearfix">
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
<!-- fileuploader -->
<script src="../js/jquery.fileuploader.min.js" type="text/javascript"></script>
<!-- Ordenar por campo de fecha -->
<script src="../js/moment.min.js" type="text/javascript"></script>
<script src="../js/datetime-moment.js" type="text/javascript"></script>

<script>
  $(document).ready(function() {
    $("#prima, #comision, #prima-neta").on("change", function() {
      var calCom = parseFloat($("#prima-neta").val()) * parseFloat($("#comision").val()) / 100;
      var comUsr = parseFloat($("#comision-usuario").val());
      calCom = calCom * <?php print $valor_uf ?> * 0.85;
      calCom = isNaN(calCom) ? 0 : calCom; //.toFixed(2);
      $("#comision-corredor").val(calCom.toLocaleString("es-CL", { style: 'currency', currency: 'CLP', maximumFractionDigits: 2 })).css("color","green");
      comUsr = isNaN(comUsr) ? 0 : comUsr;
      comUsr = calCom * comUsr / 100;
      $("#comision-valor").val(comUsr.toLocaleString("es-CL", { style: 'currency', currency: 'CLP', maximumFractionDigits: 2 })).css("color", "red")
    });
    $("#prima-neta").on("change", function() {
      var calBruta = parseFloat($("#prima-neta").val()) * 1.19;
      calBruta = isNaN(calBruta) ? 0 : calBruta.toFixed(2);
      $("#prima").val(calBruta);
    });
    $("#cierra-frm").click(function() {
      $("#div-frm").toggle();
      if($("#div-frm").is(":hidden"))
        $("html, body").animate({ scrollTop: 0 }, "slow");
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
        $("#cierra-frm").click();
    });
    $("#btnGuardar").click(function() {
      var form_data = new FormData( $("#frm-cliente")[0] );
      $("#modConfirma").modal('hide');
      
      $.ajax({type: 'POST',
        url: "ajax/tabla_cotizacion.php",
        async      : false,
				cache      : false,
				contentType: false,
				processData: false,        
        /* Mostrar splash de espera mientras llega respuesta del script php */
        beforeSend: 
          function() {
            $.showLoading({name: 'jump-pulse',allowHide: false});
          },
        data: form_data,
        /* Colocar respuesta del script php en el marco DIV indicado */
        success:
          function(result){
            oTable.fnDraw();
            $("#historial").html(result);
            if(result.indexOf('No hay cotizaciones') < 0)
              activatbl("#tbl-cotizaciones");
            $.hideLoading();
            if(SalvarNuevo == 1) {
              SalvarNuevo = 0;
              $( "#rut" ).focus();
              $("html, body").animate({ scrollTop: 0 }, "slow");
            } 
          },
        error:
          function(result){
            alert("Error de Ajax: "+result);
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
            oTable.fnDraw();
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
      var queRamo = $("#ramo option:selected").text().toLowerCase();
      if(esVehiculo)
        $("#vehiculo").show();
      else
        $("#vehiculo").hide();
      if(queRamo == "condominios")
        $("#div-condominio").show();
      else
        $("#div-condominio").hide();
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
    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    var oTable = $("#tbl-cotizaciones").dataTable({
      "info"      	 : true,
      "searching" 	 : true,
      "ordering"     : true,
      "lengthChange" : true,
      "bProcessing"  : true,
      "bServerSide"  : true,
      "sAjaxSource"  : "ajax/server-cot.php",
          "oLanguage": {
            "sProcessing": "Procesando...",
            "sLoadingRecords": "Espere por favor - cargando...",
            "sZeroRecords": "No hay registros que coincidan con su busqueda",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_",
						"oPaginate" : {
								"sFirst"   : 'Primera',
								"sPrevious": 'Ant.',
								"sNext"    : 'Sig.',
								"sLast"    : 'Ultima'
							},
						"sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
						"sInfoFiltered": "(filtrados de _MAX_ registros totales)",
						"sLengthMenu": "Monstrando _MENU_ registros",
						"sSearch"	: "Buscar:"
          }              
    });
    /** Actualizar los tados de la tabla por demanda **/
    $("#up-table").click(function() {
      oTable.fnDraw();
    });
    /** Buscar provincias y comunas **/
    /*
    $("#region, #provincia").change(function () {
          var id  = $(this).val();
          var quien = $(this).attr("id");
          var destino;
          switch(quien) {
            case "region":
              destino = $("#provincia");
              $("#comuna").html("");
              break;
            case "provincia":
              destino = $("#comuna");
              break;
          }          
          $.ajax({type: 'POST',
            url: "ajax/traeob.php",
            async: false,
            //-- Mostrar icono de espera mientras llega respuesta del script php
            beforeSend:
              function() {
                $.showLoading({name: 'jump-pulse',allowHide: false});			
              },
            data: {
                'objeto': quien,
                'id'    : id
              },
            //-- Colocar respuesta del script php en el marco DIV indicado
            success:
              function(result){
                destino.html(result);
                $.hideLoading();
              }
          });           
          
    });
    */
    $("[id='comision']").keypress(validate);
    $("[id='prima']").keypress(validate_actual);
    $("[id='prima-neta']").keypress(validate_neta);
    $("[id='monto-asegurado']").keypress(validate_monto);
		window.api = $.fileuploader.getInstance(input); 
<?php
if($cid) {
?>
    $("#div-frm").show();
<?php
}
?>
  });
  
  /* Funcion para activar Tabla dinamica */
  function activatbl(id_tabla) {
    $("#up-table").click();
  }
  /* Validar formulario de registro de Cotizaciones */
  function validar(paramGuardarNuevo) {
    var ruta=document.frm_cliente;
    var $myForm = $('#frm-cliente');
    var eFile = false;
    var ePoliza = false;
    var el2 = $("#adjunto-file").parsley();
    var el3 = $("#poliza").parsley();
    if (typeof paramGuardarNuevo === 'undefined') paramGuardarNuevo = 0;
    SalvarNuevo = paramGuardarNuevo;
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
    if($("#etapa").children(":selected").attr("is-poliza") == 'si')
      ePoliza = true;      
    el3.removeError('forcederror', {updateClass: true});
    $(el3.ulError).empty();      
    if(ePoliza === true && $("#poliza").val() === "") {
      el3.addError('forcederror', {message: 'La poliza es requerida en esta etapa'});
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
          'objeto': 'cotizacion',
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
  function downloadFile(idfile) {
    window.location.href = "descargar.php?idfile="+idfile;
  }
  function deleteFile(idfile) {
    if(!confirm("Desea Eliminar el archivo?"))
      return;
    $.ajax({type: 'POST',
      url: "ajax/borrarfile.php",
      async: false,
      //-- Mostrar icono de espera mientras llega respuesta del script php
      beforeSend:
        function() {
          $.showLoading({name: 'jump-pulse',allowHide: false});			
        },
      data: {
          'id'      : idfile,
          'modulo'  : 'cotizacion',
          'idmodulo': $("#idcotizacion").val()
        },
      //-- Colocar respuesta del script php en el marco DIV indicado
      success:
        function(result){
          $("#tabla-adjuntos").html(result);
          $.hideLoading();
        },
      error:
        function(result){
          $.hideLoading();
          ("#tabla-adjuntos").html('Error con Ajax:'+result);
        }
    });    
  }
  //-- Validar campo numerico
  function validate(evt) {
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
  function validate_actual(evt) {
     var key = window.event ? evt.keyCode : evt.which;
     var valor = document.getElementById("prima").value;
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
  function validate_neta(evt) {
     var key = window.event ? evt.keyCode : evt.which;
     var valor = document.getElementById("prima-neta").value;
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
  function validate_monto(evt) {
     var key = window.event ? evt.keyCode : evt.which;
     var valor = document.getElementById("monto-asegurado").value;
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