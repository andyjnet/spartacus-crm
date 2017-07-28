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

//-- Obtener datos de campaña
$sql = "SELECT id FROM campaign
		WHERE estado = 1
			AND NOW() BETWEEN inicio AND fin";
$query = pg_query($conn, $sql);
$campaign = 0;
if($row = pg_fetch_assoc($query))
	$campaign = $row['id'];			
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
				//--
				$csv[$row]['contacto']   = $data[13];
				$csv[$row]['fantasia']   = $data[14];
				$csv[$row]['movil']  	 = $data[15];
				$csv[$row]['region']  	 = $data[16];
				$csv[$row]['provincia']  = $data[17];
				$csv[$row]['comuna']  	 = $data[18];
				$csv[$row]['email']  	 = $data[19];
				$csv[$row]['renovacion'] = $data[20];
				$csv[$row]['producto']   = $data[21];
				$csv[$row]['condominio'] = $data[22];
				$csv[$row]['patente']  	 = $data[23];
				$csv[$row]['chasis']  	 = $data[24];
				$csv[$row]['motor']  	 = $data[25];
				$csv[$row]['cuota']  	 = $data[26];
				$csv[$row]['forma-pago'] = $data[27];
				$csv[$row]['deducible']  = $data[28];
				$csv[$row]['comision_corredor']  = $data[29];
				$csv[$row]['compania']   = $data[30];
                
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
					//- Campos nuevos (csv actualizado)
					$contacto   = trim($csv[$i]['contacto']);
					$movil 	    = trim($csv[$i]['movil']);
                    $email 	    = trim($csv[$i]['email']);
					$region	    = trim($csv[$i]['region']);
					$provincia  = trim($csv[$i]['provincia']);
					$comuna	    = trim($csv[$i]['comuna']);
					$cuota	    = $csv[$i]['cuota'];
					$com_cor    = $csv[$i]['comision_corredor'];
					$renovacion = $csv[$i]['renovacion'];
					$deducible	= $csv[$i]['deducible'];
					$patente	= $csv[$i]['patente'];
					$chasis		= $csv[$i]['chasis'];
					$motor		= $csv[$i]['motor'];
					$producto	= $csv[$i]['producto'];
					$compania 	= $csv[$i]['compania'];
					$forma_pago = $csv[$i]['forma-pago'];
					
                    // Formatos					
					$monto	   = str_replace(".","",$monto);
					$prima	   = str_replace(".","",$prima);
					$cuota	   = str_replace(".","",$cuota);
					$com_cor	= str_replace(".","",$com_cor);
					
                    $prima     = str_replace(",",".",$prima);
                    $monto     = str_replace(",",".",$monto);
					$cuota     = str_replace(",",".",$cuota);
					$com_cor   = str_replace(",",".",$com_cor);
					
					$com_cor = is_numeric($com_cor) ? $com_cor : 0.00;
					$cuota	 = is_numeric($cuota) ? $cuota : 0.00;
					
                    $cliente   = str_replace("'", "", $cliente);
                    $marca     = str_replace("'", "", $marca);
                    $modelo    = str_replace("'", "", $modelo);
					$region    = str_replace("'", "", $region);
					$provincia = str_replace("'", "", $provincia);
					$comuna    = str_replace("'", "", $comuna);
                    
                    //-- Campos de Fecha
                    try  {
                        $vence = fec_to_format($vence, "d/m/Y");
                    } catch(Exception $e) {
                      $vence = "2017-01-01";
                    }
                    try  {
                        $renovacion = fec_to_format($renovacion, "d/m/Y");
                    } catch(Exception $e) {
                      $renovacion = "2017-01-01";
                    }					
                    
                    //print("ID: [$id] Cliente: [$cliente] Rut: [$rut] Rubro: [$rubro] Prima: [$prima] Monto: [$monto] Telefono: [$telefono] Vence: [$vence] Direcccion: [$direccion]<br />");
                    if($rut != $rut_act) {
						$cliente = utf8_encode($cliente);
                        print("Procesando: $rut / $cliente...<br />");
                        $sql_rut = "lower(replace(replace('$rut','.',''),'-',''))";
                        $sql = "INSERT INTO clientes(idorigen, idejecutivo, rut, nombre, idestado, idusuario)
                                SELECT 1, $id, '$rut', '$cliente', 1, -1
                                WHERE NOT EXISTS( SELECT id FROM clientes
                                            WHERE lower(replace(replace(rut,'.',''),'-','')) = $sql_rut)
                                RETURNING id";
                        if(!$query = pg_query($conn, $sql)) die("Error con la base de datos!<br/>$sql");
                        if(!$row = pg_fetch_assoc($query)) {
                            $sql = "SELECT id FROM clientes WHERE lower(replace(replace(rut,'.',''),'-','')) = $sql_rut";
                            if(!$query = pg_query($conn, $sql)) die("Error con la base de datos!");
                            $row = pg_fetch_assoc($query);
                            $idcliente = $row['id'];
                        } else {
                            if($telefono || $direccion) {
                                $idcliente = $row['id'];
								$direccion = pg_escape_string(utf8_encode($direccion));
								$contacto = pg_escape_string(utf8_encode($contacto));
								$region =  pg_escape_string(utf8_encode($region));
								$provincia = pg_escape_string(utf8_encode($provincia));
								$comuna = pg_escape_string(utf8_encode($comuna));
                                $sql = "INSERT INTO clientes_contactos(idcliente, telefono, direccion, idusuario,
											movil, email, nombre, region, provincia, comuna)
                                        VALUES($idcliente,'$telefono','$direccion',-1,
											'$movil', '$email', '$contacto', '$region', '$provincia', '$comuna')";
                                pg_query($conn, $sql) or die("Error con la base de datos<br/>$sql");
                            }
                        }
                        $rut_act = $rut;
                    }
					$idramo = 0;
					if($rubro) {
						if($marca || $modelo || $year || $patente || $chasis || $motor)
							$veh = 1;
						else
							$veh = 0;
						$rubro = utf8_encode($rubro);
						$sql = "SELECT id FROM ramos WHERE UPPER(descripcion)=UPPER('$rubro')";
						$query = pg_query($conn, $sql) or die("Error buscando ramo<br/>$sql");
						if($row = pg_fetch_assoc($query))
							$idramo = $row['id'];
						else {
							$sql = "INSERT INTO ramos(descripcion, vehiculo)
									VALUES('$rubro', $veh)
									RETURNING id";
							$query = pg_query($conn, $sql) or die("Error creando ramo");
							if($row = pg_fetch_assoc($query))
								$idramo = $row['id'];
						}
					}
					$idproducto = 0;
					if($producto) {
						$producto = utf8_encode($producto);
						$sql = "SELECT id FROM productos WHERE UPPER(descripcion)=UPPER('$producto')";
						$query = pg_query($conn, $sql) or die("Error buscando ramo<br />$sql");
						if($row = pg_fetch_assoc($query))
							$idproducto = $row['id'];
						else {
							$sql = "INSERT INTO productos(descripcion)
									VALUES('$producto')
									RETURNING id";
							$query = pg_query($conn, $sql) or die("Error creando ramo");
							if($row = pg_fetch_assoc($query))
								$idproducto = $row['id'];
						}
					}
					$idcompania = 0;
					if($compania) {
						$compania = utf8_encode($compania);
						$sql = "SELECT id FROM companias WHERE UPPER(descripcion)=UPPER('$compania')";
						$query = pg_query($conn, $sql) or die("Error buscando ramo<br />$sql");
						if($row = pg_fetch_assoc($query))
							$idcompania = $row['id'];
						else {
							$sql = "INSERT INTO companias(descripcion)
									VALUES('$compania')
									RETURNING id";
							$query = pg_query($conn, $sql) or die("Error creando ramo");
							if($row = pg_fetch_assoc($query))
								$idcompania = $row['id'];
						}
					}
					$idfp = 0;
					if($forma_pago) {
						$forma_pago = utf8_encode($forma_pago);
						$sql = "SELECT id FROM formas_pago WHERE UPPER(descripcion)=UPPER('$forma_pago')";
						$query = pg_query($conn, $sql) or die("Error buscando forma de pago<br />$sql");
						if($row = pg_fetch_assoc($query))
							$idfp = $row['id'];
						else {
							$sql = "INSERT INTO formas_pago(descripcion)
									VALUES('$forma_pago')
									RETURNING id";
							$query = pg_query($conn, $sql) or die("Error creando forma de pago<br />$sql");
							if($row = pg_fetch_assoc($query))
								$idfp = $row['id'];
						}
					}					
                    //-- Creamos la cotizacion
					$deducible = utf8_encode($deducible);
                    $sql = "INSERT INTO cotizacion(
                                idcliente, idsucursal, idejecutivo, prima_neta, vigencia, 
                                monto_asegurado, idusuario, idcorredor, idetapa,
								renovacion, cuota, deducible, comision_corredor,
								idramo, idproducto, idcompania, idforma_pago,
								idcampaign)
                            VALUES ($idcliente, 6, $id, $prima, '$vence', 
                                $monto, -1, 5, 43,
								'$renovacion', $cuota, '$deducible', $com_cor,
								$idramo, $idproducto, $idcompania, $idfp,
								$campaign)
                            RETURNING id";
                    if(!$query = pg_query($conn, $sql)) die("Error con la base de datos!<br />$sql");
                    $row = pg_fetch_assoc($query);
                    $idcotizacion = $row['id'];
					$chasis = utf8_encode($chasis);
                    if($marca || $modelo || $year) {
						$marca = pg_escape_string(utf8_encode($marca));
						$modelo = pg_escape_string(utf8_encode($modelo));
						$motor = pg_escape_string(utf8_encode($motor));
                        $sql = "INSERT INTO cotizacion_vehiculos(
                                    idcotizacion, marca, modelo, year,
									patente, chasis, motor)
                                VALUES($idcotizacion, '$marca', '$modelo', '$year',
									'$patente', '$chasis', '$motor')";
                        pg_query($conn, $sql) or die("Error con la base de datos!<br />$sql"); 
                    }
                    
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