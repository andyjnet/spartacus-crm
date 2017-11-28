<?php
session_start();
if(!isset($_SESSION['usuario'])) {
    header("location: ../page_403.html");
}
$idusuario = $_SESSION['uid'];
include('../../includes/funciones.php');
include('../../includes/conn.php');
$id     = isset($_POST['id'])?$_POST['id']:0;
$objeto = isset($_POST['objeto'])?$_POST['objeto']:'';
if(!$id || !$objeto) exit();
switch($objeto) {
    case "region":
        $sql = "SELECT id_pr AS id,
                    str_descripcion AS descripcion
                FROM provincia_cl
                WHERE id_re = $id";
        $query = pg_query($conn, $sql);
        print '<option value="0">-- Seleccione --</option>';
        while($row = pg_fetch_assoc($query)){
            print '<option value="'.$row['id'].'">'.$row['descripcion'].'</option>';
        }
        break;
    
    case "provincia":
        $sql = "SELECT id_co AS id,
                    str_descripcion AS descripcion
                FROM comuna_cl
                WHERE id_pr = $id";
        $query = pg_query($conn, $sql);
        print '<option value="0">-- Seleccione --</option>';
        while($row = pg_fetch_assoc($query)){
            print '<option value="'.$row['id'].'">'.$row['descripcion'].'</option>';
        }
        break;
    
    case "ejecutivo":
        $sql = "SELECT rut, ingreso, nombre, apellidos, uname, clave,
                    telefono, movil, email, direccion,
                    TO_CHAR(ingreso,'DD/MM/YYYY') AS ingreso,
                    idnivel, idcargo, idsucursal, estado, idsupervisor,
                    comision, idcampaign
                FROM usuarios
                WHERE id=$id";
        $query = pg_query($conn, $sql);
        if($fila = pg_fetch_assoc($query)) {
?>
        <script>
            var esUsuario = $('input[id="chk-usuario"]:checked').length > 0;
            $("#rut").val("<?php print $fila['rut'] ?>");
            RutVar = true;
            $("#nombre").val("<?php print $fila['nombre'] ?>");
            $("#apellidos").val("<?php print $fila['apellidos'] ?>");
            $("#ingreso").val("<?php print $fila['ingreso'] ?>");
            $("#telefono").val("<?php print $fila['telefono'] ?>");
            $("#movil").val("<?php print $fila['movil'] ?>");
            $("#email").val("<?php print $fila['email'] ?>");
            $("#direccion").val("<?php print $fila['direccion'] ?>");
            $("#cargo").children().attr('selected', false);
            $("#nivel").children().attr('selected', false);
            $("#sucursal").children().attr('selected', false);
            $("#supervisor").children().attr('selected', false);
            $("#campaign").children().attr('selected', false);
            $("#cargo").children('[value="<?php print $fila['idcargo'] ?>"]').attr('selected', true);
            $("#nivel").children('[value="<?php print $fila['idnivel'] ?>"]').attr('selected', true);
            $("#sucursal").children('[value="<?php print $fila['idsucursal'] ?>"]').attr('selected', true);
            $("#supervisor").children('[value="<?php print $fila['idsupervisor'] ?>"]').attr('selected', true);
            $("#campaign").children('[value="<?php print $fila['idcampaign'] ?>"]').attr('selected', true);
            $("#cargo").val('<?php print $fila['idcargo'] ?>');
            $("#nivel").val('<?php print $fila['idnivel'] ?>');
            $("#sucursal").val('<?php print $fila['idsucursal'] ?>');
            $("#supervisor").val('<?php print $fila['idsupervisor'] ?>');
            $("#campaign").val('<?php print $fila['idcampaign'] ?>');
            $("#comision").val('<?php print $fila['comision'] ?>');
            if(esUsuario && <?php print $fila['estado']?'false':'true' ?>)
                $("#chk-usuario").trigger("click");
            $("#uname").val("<?php print $fila['uname'] ?>");
            $("#pwd-usr").remove();
            $("#idejecutivo").remove();
            $('<input>', {
                type : 'hidden',
                id   : 'idejecutivo',
                name : 'idejecutivo',
                value: '<?php print $id; ?>'
              }).appendTo('#frm-cliente');     
            $('<input>', {
                type : 'hidden',
                id   : 'pwd-usr',
                name : 'pwd-usr',
                value: '<?php print $fila["clave"]; ?>'
              }).appendTo('#frm-cliente');       
        </script>
<?php
        }
    break;

    case "cliente":
        $sql = "SELECT c.idejecutivo, c.tipo, c.rut, c.nombre,
                    c.comentarios, c.nombre_fantasia, c.idestado, 
                    cc.nombre AS contacto,
                    cc.telefono, cc.movil, cc.email,
                    cc.direccion
                FROM clientes c
                    LEFT JOIN clientes_contactos cc ON(cc.idcliente=c.id)
                WHERE c.id = $id";
        $query = pg_query($conn, $sql);
        if($fila = pg_fetch_assoc($query)) {
?>
        <script>
            $("[name=tipo]").parent().removeClass("checked");
            $("[name=tipo]").prop('checked', false);
            $("[name=tipo]").val("<?php print $fila['tipo'] ?>");
            $("#tipo<?php print $fila['tipo'] ?>").parent().addClass("checked");
            $("#tipo<?php print $fila['tipo'] ?>").prop('checked', true);
            $("#rut").val("<?php print $fila['rut'] ?>");
            RutVar = true;
            $("#nombre").val("<?php print $fila['nombre'] ?>");
            $("#nom_fantasia").val("<?php print $fila['nombre_fantasia'] ?>");
            $("#ejecutivo").children().attr('selected', false);
            $("#estado").children().attr('selected', false);
            $("#ejecutivo").children('[value="<?php print $fila['idejecutivo'] ?>"]').attr('selected', true);
            $("#estado").children('[value="<?php print $fila['idestado'] ?>"]').attr('selected', true);
            $("#ejecutivo").val('<?php print $fila['idejecutivo'] ?>');
            $("#estado").val('<?php print $fila['idestado'] ?>');
            $("#comentarios").val("<?php print $fila['comentarios'] ?>");
            $("#contacto").val("<?php print $fila['contacto'] ?>");
            $("#telefono").val("<?php print $fila['telefono'] ?>");
            $("#movil").val("<?php print $fila['movil'] ?>");
            $("#email").val("<?php print $fila['email'] ?>");
            $("#direccion").val("<?php print $fila['direccion'] ?>");
            $("#idcliente").remove();
            $('<input>', {
                type : 'hidden',
                id   : 'idcliente',
                name : 'idcliente',
                value: '<?php print $id; ?>'
              }).appendTo('#frm-cliente');             
        </script>
<?php
        }
    break;

    case "cotizacion":
        $sql = "SELECT c.id AS idcliente, c.rut, c.nombre, c.nombre_fantasia,
                    cc.nombre AS contacto,
                    cc.telefono, cc.movil, cc.email,
                    cc.direccion, cc.region, cc.provincia, cc.comuna,
                    v.patente, v.marca, v.modelo, v.year,
                    v.chasis, v.motor,
                    p.idetapa, p.idsucursal, p.idcorredor,
                    p.idejecutivo, p.idramo,
                    r.vehiculo,
                    CONCAT(p.idramo, ':', r.vehiculo) AS txt_ramo,
                    p.siniestros, p.prima_actual,
                    p.prima_neta, p.monto_asegurado,
                    to_char(p.vigencia, 'DD/MM/YYYY') AS vigencia,
                    to_char(p.renovacion, 'DD/MM/YYYY') AS renovacion,
                    p.observacion, p.poliza, p.idproducto,
                    ccon.codigo AS condominio,
                    p.cuota, p.comision_corredor AS comision, p.idforma_pago,
                    p.comision AS comision_usuario,
                    p.deducible, p.idcompania,
                    p.idcampaign
                FROM cotizacion p
                    INNER JOIN clientes c ON(p.idcliente = c.id)
                    INNER JOIN ramos r ON(p.idramo = r.id)
                    LEFT JOIN clientes_contactos cc ON(cc.idcliente=c.id)
                    LEFT JOIN cotizacion_vehiculos v ON(v.idcotizacion = p.id)
                    LEFT JOIN cotizacion_condominios ccon ON(ccon.idcotizacion = p.id)
                WHERE p.id = $id";
        $query = pg_query($conn, $sql);
        if($fila = pg_fetch_assoc($query)) {
            $observacion = json_encode($fila['observacion']) ?? '';
            $condo = '$("#div-condominio").';
            if($fila['condominio'])
                $condo .= "show();";
            else
                $condo .= "hide();"; 
?>
        <script>
            var tipoRamo = <?php print $fila['vehiculo'] ?>;
            $("#rut").val("<?php print $fila['rut'] ?>");
            $("#cliente").val("<?php print $fila['nombre'] ?>");
            $("#nom-fantasia").val("<?php print $fila['nombre_fantasia'] ?>");
            $("#nom-contacto").val("<?php print $fila['contacto'] ?>");
            $("#telefono").val("<?php print $fila['telefono'] ?>");
            $("#movil").val("<?php print $fila['movil'] ?>");
            $("#email").val("<?php print $fila['email'] ?>");
            $("#direccion").val("<?php print $fila['direccion'] ?>");
            $("#region").val("<?php print $fila['region'] ?>");
            $("#provincia").val("<?php print $fila['provincia'] ?>");
            $("#comuna").val("<?php print $fila['comuna'] ?>");
            $("#comision").val("<?php print $fila['comision'] ?>");
            $("#comision-usuario").val("<?php print $fila['comision_usuario'] ?>");
            $("#deducible").val("<?php print $fila['deducible'] ?>");
            
            /****** Selects ******/
            $("#etapa").children().attr('selected', false);
            $("#sucursal").children().attr('selected', false);
            $("#corredor").children().attr('selected', false);
            $("#ejecutivo").children().attr('selected', false);
            $("#ramo").children().attr('selected', false);
            $("#producto").children().attr('selected', false);
            $("#forma-pago").children().attr('selected', false);
            $("#cia").children().attr('selected', false);
            $("#campaign").children().attr('selected', false);
            
            $("#etapa").children('[value="<?php print $fila['idetapa'] ?>"]').attr('selected', true);
            $("#sucursal").children('[value="<?php print $fila['idsucursal'] ?>"]').attr('selected', true);
            $("#corredor").children('[value="<?php print $fila['idcorredor'] ?>"]').attr('selected', true);
            $("#ejecutivo").children('[value="<?php print $fila['idejecutivo'] ?>"]').attr('selected', true);
            $("#ramo").children('[value="<?php print $fila['txt_ramo'] ?>"]').attr('selected', true);
            $("#producto").children('[value="<?php print $fila['idproducto'] ?>"]').attr('selected', true);
            $("#forma-pago").children('[value="<?php print $fila['idforma_pago'] ?>"]').attr('selected', true);
            $("#cia").children('[value="<?php print $fila['idcompania'] ?>"]').attr('selected', true);
            $("#campaign").children('[value="<?php print $fila['idcampaign'] ?>"]').attr('selected', true);
            
            $("#etapa").val('<?php print $fila['idetapa'] ?>');
            $("#sucursal").val('<?php print $fila['idsucursal'] ?>');
            $("#ejecutivo").val('<?php print $fila['idejecutivo'] ?>');
            $("#corredor").val('<?php print $fila['idcorredor'] ?>');
            $("#ramo").val('<?php print $fila['txt_ramo'] ?>');
            $("#producto").val('<?php print $fila['idproducto'] ?>');
            $("#forma-pago").val('<?php print $fila['idforma_pago'] ?>');
            $("#cia").val('<?php print $fila['idcompania'] ?>');
            $("#campaign").val('<?php print $fila['idcampaign'] ?>');
            
            if(tipoRamo == 1)
                $("#vehiculo").show();
            else    
                $("#vehiculo").hide();
            /****** Vehiculo ******/
            $("#patente").val("<?php print $fila['patente'] ?>");
            $("#marca").val("<?php print $fila['marca'] ?>");
            $("#modelo").val("<?php print $fila['modelo'] ?>");
            $("#year").val("<?php print $fila['year'] ?>");
            $("#chasis").val("<?php print $fila['chasis'] ?>");
            $("#motor").val("<?php print $fila['motor'] ?>");
            /****** Condominios *****/
            $("#condominio").val("<?php print $fila['condominio'] ?>");
            <?php print $condo ?>
            
            $("#siniestros").val("<?php print $fila['siniestros'] ?>");
            $("#prima").val("<?php print $fila['prima_actual'] ?>");
            $("#prima-neta").val("<?php print $fila['prima_neta'] ?>");
            $("#cuota").val("<?php print $fila['cuota'] ?>");
            $("#monto-asegurado").val("<?php print $fila['monto_asegurado'] ?>");
            $("#vigencia").val("<?php print $fila['vigencia'] ?>");
            $("#renovacion").val("<?php print $fila['renovacion'] ?>");
            $("#observacion").val(<?php print $observacion ?>);
            $("#poliza").val("<?php print $fila['poliza'] ?>");
            $("#prima").trigger("change");
            
            $("#etapa-actual").remove();
            $('<input>', {
                type : 'hidden',
                id   : 'etapa-actual',
                name : 'etapa-actual',
                value: '<?php print $fila['idetapa']; ?>'
              }).appendTo('#frm-cliente');
            
            $("#idcotizacion").remove();
            $('<input>', {
                type : 'hidden',
                id   : 'idcotizacion',
                name : 'idcotizacion',
                value: '<?php print $id; ?>'
              }).appendTo('#frm-cliente');
            $("#idcliente").remove();
            $('<input>', {
                type : 'hidden',
                id   : 'idcliente',
                name : 'idcliente',
                value: '<?php print $fila['idcliente'] ?>'
              }).appendTo('#frm-cliente');

<?php            
//-- buscamos documento adjuntos a la cotizacion
$sql = "SELECT a.id AS idfile, a.ruta, a.anterior,
            e.id AS idetapa,
			e.descripcion AS etapa,
			a.size_text AS size,
			to_char(a.fecha_reg, 'DD/MM/YYYY HH24:MI:SS') AS fecha_reg,
            u.nombre AS usuario
		FROM adjuntos a
			LEFT JOIN etapas_venta e ON(a.idinterno = e.id)
            LEFT JOIN usuarios u ON(a.idusuario = u.id)
		WHERE modulo = 'cotizacion'
			AND idmodulo = $id
		ORDER BY e.orden";
$query = pg_query($conn, $sql);
$no_attach = "";
if(pg_num_rows($query) > 0) {
    $html = '$("#tabla-adjuntos").html(\'
    <div class="table-responsive">
      <table class="table table-striped jambo_table bulk_action" id="tbl-adjuntos">
        <thead>
          <tr class="headings">
          <th class="column-title text-left">Archivo  </th>
          <th class="column-title text-left">Etapa </th>
          <th class="column-title text-left">Tamaño </th>
          <th class="column-title text-left">Fecha </th>
          <th class="column-title text-left">Usuario </th>
          <th class="column-title no-link last text-center"><span class="nobr">Acci&oacute;n</span>
          </th>
          </tr>
        </thead>
        <tbody>';
    $act = 1;
    while($fila = pg_fetch_assoc($query)) {
        $clase = (($act%2)==0)?"odd pointer":"even pointer";
        $no_attach .= '$("#etapa").children("[value='.$fila['idetapa'].']").attr("is-attach", "no");'; 
        $html .='
          <tr class="<?php print $clase ?>">
            <td class=" text-left">'.$fila['anterior'].'</td>
            <td class=" text-left"><strong>'.$fila['etapa'].'</strong></td>
            <td class=" text-left">'.$fila['size'].'</td>
            <td class=" text-left">'.$fila['fecha_reg'].'</td>
            <td class=" text-left">'.$fila['usuario'].'</td>
            <td class=" last text-center">
                <a href="javascript:void(0)"
                   data-toggle="tooltip" data-placement="bottom"
                   title="Descargar '.$fila['anterior'].'"
                   onclick="downloadFile('.$fila['idfile'].');">
                    <i class="fa fa-download"></i>
                </a>        
                &nbsp;&nbsp;
                <a href="javascript:void(0)"
                   data-toggle="tooltip" data-placement="bottom"
                   title="Click para eliminar Documento: '.$fila['anterior'].'"
                   onclick="deleteFile('.$fila['idfile'].');">
                    <i class="fa fa-remove" style="color: red;"></i>
                </a>			
            </td>
          </tr>';
        $act += 1;
    }
    $html .= '
        </tbody>
      </table>
    </div>\');';
    $html = str_replace(array("\r", "\n", "\t"), '', $html);
    
} else {
    $html = '$("#tabla-adjuntos").html("");';
}
echo $html.$no_attach;
?>              

        </script>

<?php
        }        
}
pg_close($conn);
?>