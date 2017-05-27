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
<form action="example.php" method="post" enctype="multipart/form-data">
    <input type="file" name="files">
    <input type="submit">
</form>	
	<!--/ HTML content goes here -->

  	<!-- js -->
  	<script src="http://code.jquery.com/jquery-3.1.1.min.js" type="text/javascript"></script>
  	<script src="js/jquery.fileuploader.min.js" type="text/javascript"></script>
	
	<!-- opciones de fileuploader -->
	<script type="text/javascript">
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
		$("form").submit(function() {
			if(api.getFiles().length === 0) {
				alert("falta archivo!");
				return false;
			}
		});
	</script>	
</body>
</html>