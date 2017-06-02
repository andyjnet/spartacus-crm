<!DOCTYPE html html>
<html lang="es">
<head>
	<meta charset="utf-8">
  	<title>My page</title>

	<!-- css -->
  	<link href="css/jquery.fileuploader.css" media="all" rel="stylesheet">
</head>
<body>

  	<!--  HTML content goes here -->
<form id="frm-file" action="" method="post" enctype="multipart/form-data">
	texto: <input type="text" id="nombre" name="nombre"><br />
	Seleccion: <select name="opcion" id="opcion">
		<option value="1">Uno</option>
		<option value="2">Dos</option>
	</select><br />
    <input id="archivo" type="file" name="files">
    <!-- <input id="upload" type="submit"> -->
	 <button id="upload">Clic para subir</button>
</form>
<p id="error" style="color: red;"></p>
<p id="mensaje" style="color: blue;"></p>
<div id="respuesta"></div>
	<!--/ HTML content goes here -->

  	<!-- js -->
  	<script src="http://code.jquery.com/jquery-3.1.1.min.js" type="text/javascript"></script>
  	<script src="js/jquery.fileuploader.min.js" type="text/javascript"></script>
	
	<!-- opciones de fileuploader -->
	<script type="text/javascript">
	$(document).ready(function() {
		//--> bof Manipulacion de archivos
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
		window.api = $.fileuploader.getInstance(input);
		//--> eof Manipulacion de archivos
		
	    //--> bof envio de archivo con Ajax
		$('#upload').on('click', function() { 
			var form_data = new FormData( $("#frm-file")[0] );
			if(api.getFiles().length === 0) {
				$("#error").html("falta archivo!");
				return false;
			}			
			$("#mensaje").html("Mensaje: "+form_data);                             
			$.ajax({
				//-- bof mostrar progreso de subida
				xhr: function() {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						percentComplete = parseInt(percentComplete * 100);
						$("#upload").text( percentComplete + '% completado' );
						if (percentComplete === 100) {
							$("#upload").text('Clic para subir');
						}
			  
					}
					}, false);
					return xhr;
				},
				url: 'example_ajax.php', 
				dataType: 'text',  
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,                         
				type: 'post',
				beforeSend: function() {
					$("#upload").attr('disabled', true);
				},
				success: function(php_script_response){
					$("#respuesta").html("<strong>Respuesta Ajax: </strong>"+php_script_response);
					$("#error").html("");
					$("#upload").text('Clic para subir').attr("disabled", false);
				},
				error:
				  function(php_script_response) {
					  $("#error").html("<strong>Error en Ajax: </strong>"+php_script_response);
					  $("#respuesta").html("");
					  $("#upload").text('Clic para subir').attr("disabled", false);
				  }				
			 });
			return false;
		});
		//<-- eof envio de archivos con Ajax
	});
	</script>	
</body>
</html>