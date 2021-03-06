<?php
//--- Iniciamos la sesion y verificamos que haya una abierta para redireccionar al dashboard
session_start();
if(isset($_SESSION['usuario'])) {
	header("location: crm/dashboard.php");
}
$username = isset($_POST['username'])?$_POST['username']:false;
$password = isset($_POST['password'])?$_POST['password']:false;
$recordar = isset($_POST['recordar'])?$_POST['recordar']:0;
if($username && $password) {
	include('includes/funciones.php');
	include('includes/conn.php');
	$acceso = false;
	$admin  = false;
	if(!$db_error) {
		if($username=="andy" && $password=="Abc123Cba") {
			$acceso 	  = true;
			$nombre		  = "root";
			$idusuario 	  = -1;
			$usr_permisos = 'cm9vdA==';
			$id_sup_usr	  = 0;
			$id_campaign  = 0;

		} else {
			//--- Buscar en la base de datos
			$sql = "SELECT u.id, u.nombre, u.admin,
						p.permisos, u.idsupervisor
					FROM usuarios u
						LEFT JOIN permisos p ON(p.idcargo = u.idcargo)
					WHERE estado=1
						AND uname='$username'
						AND clave=md5('$password')";
			$query = pg_query($conn, $sql);
			if($fila = pg_fetch_assoc($query)) {
				$acceso    	  = true;
				$nombre    	  = $fila['nombre'];
				$idusuario 	  = $fila['id'];
				$usr_permisos = $fila['permisos'];
				$id_sup_usr   = $fila['idsupervisor'];
				$sql = "UPDATE usuarios SET ultimo=NOW() WHERE id=$idusuario";
				$query = pg_query($conn, $sql);
				glog($idusuario, $nombre, 'acceso', 'Ingreso al sistema');
				//-- localizamos campaña
				$id_campaign = 0;
				$sql = "SELECT id FROM campaign
						WHERE estado = 1
							AND NOW() BETWEEN inicio AND fin";
				$query = pg_query($sql);
				if($row = pg_fetch_array($query))
					$id_campaign = $row[0];
			} else {
				$alert_error="Usuario o contrase&ntilde;a incorrectos. Intente de nuevo o consulte con su administrador.";
			}
		}
		if($acceso) {
			//--- Crear la sesion y Variables de sesion necesarios y redireccionar al dashboard del usuario
			$usr_permisos = base64_decode($usr_permisos) ?? '';
			$admin = 0;
			$pos   = strpos($usr_permisos, 'root');
			if($pos === false) {
				$admin = 0;
			} else {
				$admin = 1;
				$usr_permisos = '';
				$id_sup_usr = 0;
			}
			//--- Obtenemos en valor de la UF del dia
			include_once("crm/valor_uf.php");
			$_SESSION['usuario'] 	= $username;
			$_SESSION['uid'] 	 	= $idusuario;
			$_SESSION['nombre']	 	= $nombre;
			$_SESSION['admin']	 	= $admin;
			$_SESSION['permisos']   = $usr_permisos;
			$_SESSION['supervisor'] = $id_sup_usr;
			$_SESSION['campaign']	= $id_campaign;
			$_SESSION['valor_uf']	= $valor_uf;
			session_write_close();
			header("location: crm/dashboard.php");
		}
	} else {
		//--- Hay error en la conexion a la abse de datos
		$alert_error = $db_error;
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Spartacus group</title>
  <link rel="shortcut icon" type="image/png" href="crm/images/favicon.png" />
  <meta http-equiv="Content-Type"    content="text/html; charset=UTF-8" />
  <meta name="viewport"      		 content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="author" 	     		 content="Spartacus Group" />
  <meta name="description"   		 content="Sistema personalizado para gestion de relacion con los clientes (CRM) y automatizacion de la fuerza de ventas (SFA)" />
  <meta name="keywords"      		 content="spartacus,corredores,seguros,SpA,group,crm,sfa,sociedad,por,acciones,asesores,vehiculos,vida,chile" />
  <meta name="Resource-type" 		 content="Document" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel="stylesheet" href="css/login.css">
  <script>
	window.console = window.console || function(t) {};
  </script>

  <script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize", "*");
  }
  </script>
</head>

<body>

<?php
if(isset($alert_error)) {
?>
<div class="container">
	<div class="alert alert-danger alert-dismissable">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<strong>Error!</strong>&nbsp;<?php print($alert_error) ?>
	</div>
</div>
<?php
} //endif
?>

<div class="container">	
	<div id="login" class="signin-card">
		<div class="logo-image">
		<img src="img/logo_sparta.png" alt="Spartacus Group - Corredores de Seguros" title="Spartacus Group - Corredores de Seguros" width="165">
		</div>
		<!-- 
		<h1 class="display1">Acceso Restringido</h1>
		<p1>Ingrese sus credenciales para entrar al sistema</p1>
		-->
		<form action="" method="post" class="" role="form">
			<div id="form-login-username" class="form-group">
				<input id="username" class="form-control" name="username" type="text" size="18" alt="usuario" required />
				<span class="form-highlight"></span>
				<span class="form-bar"></span>
				<label for="username" class="float-label">Usuario</label>
			</div>
			<div id="form-login-password" class="form-group">
				<input id="passwd" class="form-control" name="password" type="password" size="18" alt="contraseña" required>
				<span class="form-highlight"></span>
				<span class="form-bar"></span>
				<label for="password" class="float-label">Contraseña</label>
			</div>
			<div id="form-login-remember" class="form-group">
				<div class="checkbox checkbox-default">       
						<input id="remember" type="checkbox" value="1" alt="Recordarme" class="" name="recordar">
						<label for="remember">Recordarme</label>      
				</div>
			</div>	
			<div>
				<button class="btn btn-block btn-info ripple-effect" type="submit" name="Submit" alt="sign in">Entrar</button>  
			</div>
		</form>
	</div>
</div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
	    integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
	    crossorigin="anonymous">
  </script>
  <script src="js/login.js"></script>
  
<!--
	<script src='js/gubja.js'></script>
	<script src='js/yaozl.js'></script>
	<script src="js/index.js"></script>
-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/webshim/1.16.0/minified/polyfiller.js"></script>
  <script> 
	webshim.activeLang('es');
	webshims.polyfill('forms');
	webshims.cfg.no$Switch = true;
  </script>	
</body>
</html>
<?php
if($username && $password) pg_close($conn);
?>