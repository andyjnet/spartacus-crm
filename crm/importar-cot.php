<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
include_once('../includes/funciones.php');
include_once('../includes/conn.php');

$username	  = isset($_SESSION['usuario'])?$_SESSION['usuario']:'';
$idusuario	  = isset($_SESSION['uid'])?$_SESSION['uid']:0;
$usr_nombre	  = isset($_SESSION['nombre'])?$_SESSION['nombre']:'';
$usr_admin	  = $_SESSION['admin'] ?? 0;
$usr_permisos = $_SESSION['permisos'] ?? '';

$csv = array();
if(isset($_FILES['csv']) && $_FILES['csv']['error'] == 0) {
    $name = $_FILES['csv']['name'];
    $ext = explode('.', $_FILES['csv']['name']);
    $ext = strtolower(end($ext));
    $type = $_FILES['csv']['type'];
    $tmpName = $_FILES['csv']['tmp_name'];

    // check the file is a csv
    if($ext === 'csv') {
        //echo("si es un csv!<br />");
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            // necessary if a large csv file
            set_time_limit(0);
            $row = 0;
            while(($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // number of fields in the csv
                $col_count = count($data);

                // get the values from the csv
                // ID;CLIENTE;RUT;TELÉFONO;RUBRO;PRIMA;VENC.;MONTO ASEG;MARCA;MODELO;AÑO;DIRECCIÓN;SUC
                $csv[$row]['id']        = $data[0];
                $csv[$row]['cliente']   = $data[1];
                $csv[$row]['rut']       = $data[2];
                $csv[$row]['telefono']  = $data[3];
                $csv[$row]['rubro']     = $data[4];
                $csv[$row]['prima']     = $data[5];
                $csv[$row]['vence']     = $data[6];
                $csv[$row]['monto']     = $data[7];
                $csv[$row]['marca']     = $data[8];
                $csv[$row]['modelo']    = $data[9];
                $csv[$row]['year']      = $data[10];
                $csv[$row]['direccion'] = $data[11];
                $csv[$row]['sucursal']  = $data[12];
                
                // inc the row
                $row++;
            }
            fclose($handle);
            // bof Manipulamos datos
            if($row) {              
                // Recorremos el vector de datos (csv)
                $rut_act = '';
                pg_query($conn, "BEGIN") or die("Error comenzando transaccion!");
                for($i=1; $i < $row; $i++) {
                    $id       = @$csv[$i]['id'];
                    $cliente  = @trim($csv[$i]['cliente']);
                    $rut      = @trim($csv[$i]['rut']);
                    if(!$id && !$cliente && !$rut) break;
                    $telefono = trim($csv[$i]['telefono']);
                    $rubro    = trim($csv[$i]['rubro']);
                    $prima    = $csv[$i]['prima'];
                    $vence    = $csv[$i]['vence'];
                    $monto    = $csv[$i]['monto'];
                    $marca    = trim($csv[$i]['marca']);
                    $modelo   = trim($csv[$i]['modelo']);
                    $year     = $csv[$i]['year'];
                    $direccion= trim($csv[$i]['direccion']);
                    $sucursal = trim($csv[$i]['sucursal']);
                    
                    // Formatos
                    $prima    = str_replace(",",".",$prima);
                    $monto    = str_replace(",",".",$monto);
                    $cliente  = str_replace("'", "", $cliente);
                    $marca    = str_replace("'", "", $marca);
                    $modelo   = str_replace("'", "", $modelo);
                    
                    //-- Campos de Fecha
                    try  {
                        $vence = fec_to_format($vence, "d-m-Y");
                    } catch(Exception $e) {
                      $vence = "2017-01-01";
                    }
                    
                    //print("ID: [$id] Cliente: [$cliente] Rut: [$rut] Rubro: [$rubro] Prima: [$prima] Monto: [$monto] Telefono: [$telefono] Vence: [$vence] Direcccion: [$direccion]<br />");
                    if($rut != $rut_act) {
                        print("Procesando: $rut / $cliente...<br />");
                        $sql_rut = "lower(replace(replace('$rut','.',''),'-',''))";
                        $sql = "INSERT INTO clientes(idorigen, idejecutivo, rut, nombre, idestado, idusuario)
                                SELECT 1, $id, '$rut', '$cliente', 1, -1
                                WHERE NOT EXISTS( SELECT id FROM clientes
                                            WHERE lower(replace(replace(rut,'.',''),'-','')) = $sql_rut)
                                RETURNING id";
                        if(!$query = pg_query($conn, $sql)) die("Error con la base de datos!");
                        if(!$row = pg_fetch_assoc($query)) {
                            $sql = "SELECT id FROM clientes WHERE lower(replace(replace(rut,'.',''),'-','')) = $sql_rut";
                            if(!$query = pg_query($conn, $sql)) die("Error con la base de datos!");
                            $row = pg_fetch_assoc($query);
                            $idcliente = $row['id'];
                        } else {
                            if($telefono || $direccion) {
                                $idcliente = $row['id'];
                                $sql = "INSERT INTO clientes_contactos(idcliente, movil, direccion, idusuario)
                                        VALUES($idcliente,'$telefono','$direccion',-1)";
                                pg_query($conn, $sql) or die("Error con la base de datos");
                            }
                        }
                        $rut_act = $rut;
                    }
                    //-- Creamos la cotizacion
                    $sql = "INSERT INTO cotizacion(
                                idcliente, idsucursal, idramo, idejecutivo, prima_actual, vigencia, renovacion,
                                monto_asegurado, idusuario, idcorredor, idetapa)
                            VALUES ($idcliente, 6, 5, $id, $prima, '$vence', '$vence',
                                $monto, -1, 5, 43)
                            RETURNING id";
                    if(!$query = pg_query($conn, $sql)) die("Error con la base de datos!");
                    $row = pg_fetch_assoc($query);
                    $idcotizacion = $row['id'];
                    if($marca || $modelo || $year) {
                        $sql = "INSERT INTO cotizacion_vehiculos(
                                    idcotizacion, marca, modelo, year)
                                VALUES($idcotizacion, '$marca', '$modelo', '$year')";
                        pg_query($conn, $sql) or die("Error con la base de datos!"); 
                    }
                    
                    //$sql="INSERT INTO productos(descripcion,referencia) VALUES('$des','$ref');";
                    //$query=mysql_query($sql,$connection);
                    //if(!$query) die(mysql_error());
                }
                if(!$query = pg_query($conn, "COMMIT")) die("Error e Commit!");
            }
            echo("Listo!");

        }
    }
} 
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"      content="width=device-width, initial-scale=1">
        <meta name="author" 	   content="Spartacus Group" />
        <meta name="description"   content="Sistema personalizado para gestion de relacion con los clientes (CRM) y automatizacion de la fuerza de ventas (SFA)" />
        <meta name="keywords"      content="spartacus,corredores,seguros,SpA,group,crm,sfa,sociedad,por,acciones,asesores,vehiculos,vida,chile" />
        <meta name="Resource-type" content="Document" />    
    
        <title>Spartacus group</title>
        <link rel="shortcut icon" type="image/png" href="images/favicon.png" />
        
        <!-- Bootstrap -->
        <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">        
    </head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
      Importar Datos:
      <input type="file" name="csv" value="examinar" /><br />
      <input id='btnSubir' type="submit" name="submit" value="Procesar" class = "btn btn-primary" />    
    </form>
    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>     
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#btnSubir').click(function() {
                $(this).attr('value', 'enviando...');
                $(this).attr('disabled', true);
            });
        });
    </script>
</body>
</html>