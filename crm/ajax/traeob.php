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
    case "ejecutivo":
        $sql = "SELECT rut, ingreso, nombre, apellidos, uname, clave,
                    telefono, movil, email, direccion,
                    TO_CHAR(ingreso,'DD/MM/YYYY') AS ingreso,
                    idnivel, idcargo, idsucursal, estado
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
            $("#cargo").children('[value="<?php print $fila['idcargo'] ?>"]').attr('selected', true);
            $("#nivel").children('[value="<?php print $fila['idnivel'] ?>"]').attr('selected', true);
            $("#sucursal").children('[value="<?php print $fila['idsucursal'] ?>"]').attr('selected', true);
            $("#cargo").val('<?php print $fila['idcargo'] ?>');
            $("#nivel").val('<?php print $fila['idnivel'] ?>');
            $("#sucursal").val('<?php print $fila['idsucursal'] ?>');
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
}
pg_close($conn);
?>