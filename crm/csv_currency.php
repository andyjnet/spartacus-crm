<?php
//-- Control de sesion
session_start();
if(!isset($_SESSION['usuario'])) {
	header("location: ../page_403.html");
}
include('../includes/conn.php');
include('../includes/funciones.php');
$csv = array();
$idmoneda 		= isset($_POST['idmoneda'])?$_POST['idmoneda']:0;
$idmoneda_valor = isset($_POST['idmoneda_valor'])?$_POST['idmoneda_valor']:0;
if($_FILES['file']['error'] == 0 && $idmoneda && $idmoneda_valor){
    $name = $_FILES['file']['name'];
    $separado = explode('.', $_FILES['file']['name']);
    $ext = strtolower(end($separado));
    $type = $_FILES['file']['type'];
    $tmpName = $_FILES['file']['tmp_name'];

    // check the file is a csv
    if($ext === 'csv'){
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            // necessary if a large csv file
            set_time_limit(0);
            $row = 0;
            while(($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // number of fields in the csv
                $col_count = count($data);
				if($col_count >= 2 && strpos($data[0],"/") && strlen($data[0]) == 10) {
					// get the values from the csv
					$fecha = $data[0];
					$csv[$row]['fecha'] = substr($fecha, 6, 4).'-'.substr($fecha, 3, 2).'-'.substr($fecha,0,2);
					$csv[$row]['monto'] = str_replace(",",".",$data[1]);
					$row++;
				}
            }
            fclose($handle);
            //--> bof Manipulamos datos
            if($row) {
                // Recorremos el vector de datos (csv)
			   pg_query($conn, "BEGIN") or txt_log("Error Abriendiendo transaccion (begin), linea 40 csv_currency.php");
               for($i=1; $i < $row; $i++) {
                    $fecha	= $csv[$i]['fecha']; 
                    $monto 	= $csv[$i]['monto'];
					$sql="DELETE FROM monedas_valor
						  WHERE idmoneda = $idmoneda
							AND idmoneda_valor = $idmoneda_valor
							AND fecha = '$fecha'";
					$query=pg_query($conn, $sql);
                    $sql="INSERT INTO monedas_valor(idmoneda, idmoneda_valor, fecha, valor)
						  VALUES($idmoneda, $idmoneda_valor, '$fecha', $monto)";
                    $query=pg_query($conn, $sql);
               }
			   pg_query($conn, "COMMIT") or txt_log("Error haciendo commit de trnasaccion, linea 53 csv_currency.php");
            }
			//<-- eof
        }
    }
}  else {
	if ($_FILES['file']['error']) {
		txt_log("Error procesando archivo, numero: ".$_FILES['file']['error']);
	} else {
		txt_log("Faltan variables idmoneda($idmoneda) o idmoneda_valor($idmoneda_valor)");
	}
}
?>