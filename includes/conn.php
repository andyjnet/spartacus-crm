<?php
/**************************************************
 *  Configurar parametros de conexion a la    
 *  Base de datos.                            
 *  Hay que llamar al archivo: funciones.php       
 *  antes que este para cargar la funcion txt_log  
 **************************************************/
$server = "localhost";
$port   = "5432";
$usr    = "usrdev";
$pwd    = "Abc123Cba";
$base   = "sparta_crm";

//*** Realizar la conexión
$db_error = false;
$conn = @pg_connect("host=$server port=$port dbname=$base user=$usr password=$pwd options='--client_encoding=UTF8'")
          or $db_error=true;
          
//*** Si hay algun error lo reportamos al log de acciones
if ($db_error) {
    $str_error = "Error conectando a PostgreSQL, verifique la configuracion\n";
    txt_log ($str_error);
}
?>