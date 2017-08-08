<?php
	//http://192.168.1.101/res/datatable/server-cot.php?sEcho=12&iColumns=9&sColumns=&iDisplayStart=0
	// &iDisplayLength=10 &mDataProp_0=0 &mDataProp_1=1 &mDataProp_2=2 &mDataProp_3=3
	// &mDataProp_4=4&mDataProp_5=5&mDataProp_6=6&mDataProp_7=7&mDataProp_8=8&sSearch=&bRegex=false&sSearch_0=&bRegex_0=false&bSearchable_0=true&sSearch_1=&bRegex_1=false&bSearchable_1=true&sSearch_2=&bRegex_2=false&bSearchable_2=true&sSearch_3=&bRegex_3=false&bSearchable_3=true&sSearch_4=&bRegex_4=false&bSearchable_4=true&sSearch_5=&bRegex_5=false&bSearchable_5=true&sSearch_6=&bRegex_6=false&bSearchable_6=true&sSearch_7=&bRegex_7=false&bSearchable_7=true&sSearch_8=&bRegex_8=false
	// &bSearchable_8=true&
	// iSortCol_0=6 &sSortDir_0=asc &iSortingCols=1 &bSortable_0=true&bSortable_1=true
	// &bSortable_2=true&bSortable_3=true&bSortable_4=true&bSortable_5=true&bSortable_6=true
	// &bSortable_7=true&bSortable_8=true&_=1497710824338
	// WHERE (id::text ILIKE '%me%'
	//  OR nombre::text ILIKE '%me%'
	//	OR telefono::text ILIKE '%me%' OR movil::text ILIKE '%me%' OR email::text ILIKE '%me%' OR ramo::text ILIKE '%me%' OR etapa::text ILIKE '%me%' OR prima_neta::text ILIKE '%me%' OR prima_actual::text ILIKE '%me%' OR vigencia::text ILIKE '%me%' OR ejecutivo::text ILIKE '%me%' OR patente::text ILIKE '%me%' OR marca::text ILIKE '%me%' OR modelo::text ILIKE '%me%' )
	/* Columnas de la base de datos a mostrar en tabla */
	$aColumns = array( 'id','nombre','telefono','movil','email','ramo','etapa','prima_neta','prima_actual','vigencia','ejecutivo','patente','marca','modelo');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "usuarios";
	
	include_once("conn.php");
	
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
	if ( isset( $iSortCol_0 ) ) {
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
		} 
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables
	 */
//cc.telefono, cc.movil, cc.email,
//				r.descripcion AS ramo,
//				ev.descripcion AS etapa,
//				p.prima_neta, p.prima_actual,
//				to_char(p.vigencia,'DD/MM/YYYY') AS vigencia,
//				u.nombre AS ejecutivo,
//				cv.patente, cv.marca, cv.modelo, cv.year,
//				p.item,
//				c.rut,	
	$sWhere = "";
	if ( isset($sSearch) && $sSearch != "")
	{
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
			WHERE p.id>0
			$sWhere
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
		FROM cotizacion
		WHERE id>0
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
		$link_edit = '<a href="#"  data-toggle="tooltip" '.
			'data-placement="bottom" title="Click para editar registro" '.
			'onclick="fn_modifica(' . $aRow['id'] . ');"  '.
			'id="a-editar' . $aRow['id'] . '">'.
			'<i class="fa fa-edit" style="color: green;"></i>'.
			'</a>';
		$link_delete = '&nbsp;&nbsp;'.
			'<a href="javascript:void(0)" '.
			'data-toggle="tooltip" data-placement="bottom" '.
			'title="Click para eliminar registro"'.
			'onclick="fn_elimina(' . $aRow['id'] .',\''. $aRow['nombre'] .'\');">'.
			'<i class="fa fa-remove" style="color: red;"></i>'.
			'</a>';
		/*
		 * Columnas de la tabla
		 */
		$row[] = $aRow['id'];
		$row[] = $aRow['nombre'];		
		$row[] = $contacto;
		$row[] = $auto;
		$row[] = $aRow['etapa'];
		$row[] = number_format($aRow['prima_actual'], 2, ',', '.');
		$row[] = $aRow['vigencia'];
		$row[] = $aRow['ejecutivo'];
		$row[] = $link_edit . $link_delete;
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>