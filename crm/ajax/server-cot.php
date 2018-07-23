<?php
	session_start();
	if(!isset($_SESSION['usuario'])) {
		header("location: ../page_403.html");
	}
	$idusuario    = $_SESSION['uid'];
	$usr_admin    = $_SESSION['admin'];
	$usr_nombre   = $_SESSION['nombre'];
	$id_campaign  = $_SESSION['campaign'];
	include_once('../../includes/funciones.php');
	include_once('../../includes/conn.php');
	if(file_exists('../../includes/tools.php'))
		include_once('../../includes/tools.php');
	if(file_exists('../includes/tools.php'))
		include_once('../includes/tools.php');	
	//-- Verificar permisos sobre etapas
	$usr_permisos = $_SESSION['permisos'] ?? '';
	$usr_etapas   = '';
	$usr_cotizaciones = '';
	if($usr_permisos && !$usr_admin) {
		$access = explode(",", $usr_permisos);
		foreach ($access as $item) {
			if(intval($item) > 1000) {
				$usr_etapas .= (intval($item) - 1000).',';
			}
		}
		if($usr_etapas) {
			$usr_etapas = substr($usr_etapas, 0, strlen($usr_etapas) - 1);
			$usr_etapas = " OR p.idetapa IN($usr_etapas) ";
		} else {
			$usr_cotizaciones = " OR p.idejecutivo = $idusuario ";
		}
	} else {
		$access = array("");
	}	
	
	/* Columnas de la base de datos a mostrar en tabla */
	$aColumns = array( 'id','nombre','telefono','movil','email','ramo','etapa','prima_neta','prima_actual','vigencia','ejecutivo','patente','marca','modelo');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "usuarios";
	
	/*
	 * Variables GET
	 */
	$iDisplayStart  = $_GET['iDisplayStart'];
	$iDisplayLength = $_GET['iDisplayLength'];
	$iSortCol_0 	= $_GET['iSortCol_0'];
	$iSortingCols	= $_GET['iSortingCols'];
	$sSearch 		= $_GET['sSearch'];
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $iDisplayStart ) && $iDisplayLength != '-1' )
	{

		$sLimit = "LIMIT ".intval( $iDisplayLength ).
			" OFFSET ".intval( $iDisplayStart );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $iSortCol_0 ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $iSortingCols ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				switch(intval( $_GET['iSortCol_'.$i] )) {
				case 2:
					$columna = "email, telefono, movil";
					break;
				case 3:
					$columna = "marca, modelo, year";
					break;
				case 4:
					$columna = "etapa";
					break;
				case 5:
					$columna = "prima_actual";
					break;
				case 6:
					$columna = "vigencia_date";
					break;
				case 7:
					$columna = "ejecutivo";
					break;
				default:
					$columna = "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ];
				}
				$sOrder .= $columna." ";
				$sOrder .= ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" ) {
			$sOrder = "";
		} else {
			//-- Dar el formato correcto para ordenar por fechas
			$sOrder = str_replace("fecha_reg", "fecha_reg::date", $sOrder);
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables
	 */
	$sWhere = "";
	if ( isset($sSearch) && $sSearch != "" ) {
		$sSearch = pg_escape_string( $sSearch );
		$sWhere = "AND (";
		$sWhere .= "c.nombre ILIKE '%".$sSearch."%'";
		$sWhere .= " OR c.nombre_fantasia ILIKE '%".$sSearch."%'";
		$sWhere .= " OR cc.telefono LIKE '%".$sSearch."%'";
		$sWhere .= " OR cc.movil LIKE '%".$sSearch."%'";
		$sWhere .= " OR to_char(p.vigencia,'DD/MM/YYYY') LIKE '%".$sSearch."%'";
		$sWhere .= " OR u.nombre ILIKE '%".$sSearch."%'";
		$sWhere .= " OR cv.marca ILIKE '%".$sSearch."%'";
		$sWhere .= " OR cv.modelo ILIKE '%".$sSearch."%'";
		$sWhere .= " OR ev.descripcion ILIKE '%".$sSearch."%'";
		if(is_numeric($sSearch)) {
			$sWhere .= " OR p.id::text LIKE '".$sSearch."%'";
			$sWhere .= " OR p.prima_neta = ".$sSearch;
			$sWhere .= " OR p.prima_actual = ".$sSearch;
			$sWhere .= " OR cv.year = '".$sSearch."'";
			if(strlen($sSearch) > 4)
				$sWhere .= " OR REPLACE(REPLACE(c.rut,'.',''),'-','') = '".$sSearch."%'";
		} 
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]."::text ILIKE '%".pg_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sql_campaign = "";
	if($id_campaign)
		$sql_campaign = " AND ( p.idcampaign = $id_campaign OR p.idetapa IN(50, 51, 44, 46, 47, 48) ) "; 
	$sQuery = "
		SELECT *
			,count(*) OVER() AS total_count
		FROM(
			SELECT p.id, 
				(CASE WHEN c.tipo='J' AND c.nombre_fantasia<>'' 
				  THEN c.nombre_fantasia
				  ELSE c.nombre
				END) AS nombre,
				cc.telefono, cc.movil, cc.email,
				r.descripcion AS ramo,
				ev.descripcion AS etapa,
				p.prima_neta, p.prima_actual,
				to_char(p.vigencia,'DD/MM/YYYY') AS vigencia,
				u.nombre AS ejecutivo,
				cv.patente, cv.marca, cv.modelo, cv.year,
				p.item,
				c.rut,
				p.vigencia AS vigencia_date
			FROM cotizacion p
			INNER JOIN clientes c ON(p.idcliente = c.id)
			INNER JOIN ramos r ON(p.idramo = r.id)
			INNER JOIN etapas_venta ev ON(p.idetapa = ev.id)
			INNER JOIN usuarios u ON(p.idejecutivo = u.id)
			LEFT JOIN clientes_contactos cc ON(cc.idcliente = c.id)
			LEFT JOIN cotizacion_vehiculos cv ON(cv.idcotizacion = p.id)
			WHERE p.id>0 AND p.estado > -1
				$sWhere
				AND ( $usr_admin = 1 $usr_cotizaciones $usr_etapas OR u.idsupervisor = $idusuario)
				$sql_campaign
		) AS t
		$sOrder
		$sLimit
			";	


	///* Formatos de campos especiales (Fecha / Currency) */
	//if(strpos($sQuery, "fecha_reg"))
	//	$sQuery = str_replace(",fecha_reg", ",to_char(fecha_reg, 'DD/MM/YYYY') AS fecha_reg", $sQuery);
	 	
	$rResult = pg_query($conn, $sQuery) or die("Error con la base de datos!");
	
	/* Data set length after filtering */
	$rResultFilterTotal = pg_query( $conn, $sQuery ) or die(mysql_error());
	$aResultFilterTotal = pg_fetch_assoc($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal['total_count'];
	
	
	/* Total data set length */
	$sQuery = "
		SELECT count(*) AS total_records
		FROM cotizacion p
		WHERE p.id>0 AND p.estado > -1
		AND ( $usr_admin=1 OR p.idejecutivo = $idusuario)
		$usr_etapas
		$sql_campaign
	";
	
	$rResultTotal = pg_query($conn, $sQuery);
	$aResultTotal = pg_fetch_assoc($rResultTotal);
	$iTotal =  $aResultTotal['total_records'];
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = pg_fetch_assoc($rResult) )
	{
		$row = array();	
		/*
		 * Formato de Celdas
		 */
		if($aRow['telefono'] <> '' && $aRow['movil'] <> '') {
			$telefono = $aRow['telefono'].' / '.$aRow['movil'];
		} elseif($aRow['telefono'] <> '') {
			$telefono = $aRow['telefono'];
		} elseif($aRow['movil'] <> '') {
			$telefono = $aRow['movil'];
		} else {
			$telefono = "";
		}
		if($aRow['email'])
			$email = $aRow['email'];
		else
			$email= "";
		if($telefono && $email)
			$contacto = $email.' / '.$telefono;
		else
			$contacto = ($telefono)?$telefono:$email;		
		$auto = $aRow['marca'].'/'.$aRow['modelo'].'/'.$aRow['year'];
		$auto = str_replace("//", "/", $auto);
		if(substr($auto,0,1) == '/') $auto = substr($auto, 1);
		if(substr($auto, strlen($auto) - 1, 1) == '/') $auto = substr($auto, 0, strlen($auto)-1);
		$link_edit = '';
		$link_delete = '';
		if($usr_admin == 1 || comprueba($usr_permisos, "9")) {
			$link_edit = '<a href="#"  data-toggle="tooltip" '.
				'data-placement="bottom" title="Click para editar registro" '.
				'onclick="fn_modifica(' . $aRow['id'] . ');"  '.
				'id="a-editar' . $aRow['id'] . '">'.
				'<i class="fa fa-edit" style="color: green;"></i>'.
				'</a>';
		}
		if($usr_admin == 1 || comprueba($usr_permisos, "10")) {		
			$link_delete = '&nbsp;&nbsp;'.
				'<a href="javascript:void(0)" '.
				'data-toggle="tooltip" data-placement="bottom" '.
				'title="Click para eliminar registro"'.
				'onclick="fn_elimina(' . $aRow['id'] .',\''. $aRow['nombre'] .'\');">'.
				'<i class="fa fa-remove" style="color: red;"></i>'.
				'</a>';
		}
		/*
		 * Columnas de la tabla
		 */
		$row[] = $aRow['id'];
		$row[] = $aRow['nombre'];		
		$row[] = $contacto;
		$row[] = $auto;
		$row[] = $aRow['etapa'];
		$row[] = number_format($aRow['prima_neta'], 2, ',', '.');
		$row[] = $aRow['vigencia'];
		$row[] = $aRow['ejecutivo'];
		$row[] = $link_edit . $link_delete;
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>