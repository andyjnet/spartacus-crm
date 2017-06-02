<?php
	$nombre = $_POST['nombre'];
	$sel 	= $_POST['opcion'];
	echo("<p>campo nombre: $nombre<br />Opcion: $sel</p>");
    include('includes/class.fileuploader.php');
	
    // initialize the FileUploader
    $FileUploader = new FileUploader('files', array(
        // Options will go here
    ));
	
    // call to upload the files
    $upload = $FileUploader->upload();
	
    if($upload['isSuccess']) {
        // get the uploaded files
        $files = $upload['files'];
    }
    if($upload['hasWarnings']) {
        // get the warnings
        $warnings = $upload['warnings'];
    };
	
	// manipular error
	if(isset($warnings)) {
		print_r($warnings);
		exit;		
	}
		
	// si todo va bien, aqui van los procedimientos sobre los archivos
	if(isset($files)) {
		echo '<pre>';
		print_r($files);
		echo '</pre>';
		echo "Archivo: ".$files[0]['name'];
	}
?>