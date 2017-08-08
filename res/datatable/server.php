<?php
	/* Columnas de la base de datos a mostrar en tabla */
	$aColumns = array( 'id', 'rut', 'nombre', "fecha_reg" );
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "usuarios";
	
	include_once("conn.php");
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{

		$sLimit = "LIMIT ".intval( $_GET['iDisplayLength'] ).
			" OFFSET ".intval( $_GET['iDisplayStart'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
					($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
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
	$sWhere = "WHERE id>0";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE id>0 AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]."::text ILIKE '%".pg_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE id>0 AND ";
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
		SELECT ".implode(",", $aColumns)."
			,count(*) OVER() AS total_count
		FROM $sTable
		$sWhere
		$sOrder
		$sLimit
		";
	/* Formatos de campos especiales (Fecha / Currency) */
	if(strpos($sQuery, "fecha_reg"))
		$sQuery = str_replace(",fecha_reg", ",to_char(fecha_reg, 'DD/MM/YYYY') AS fecha_reg", $sQuery);
	 	
	$rResult = pg_query($conn, $sQuery) or die("Error con la base de datos!");
	
	/* Data set length after filtering */
	$rResultFilterTotal = pg_query( $conn, $sQuery ) or die(mysql_error());
	$aResultFilterTotal = pg_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[count($aColumns)];	
	
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
		WHERE id>0
	";
	
	$rResultTotal = pg_query($conn, $sQuery);
	$aResultTotal = pg_fetch_array($rResultTotal);
	$iTotal =  $aResultTotal[0];
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = pg_fetch_array($rResult) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>