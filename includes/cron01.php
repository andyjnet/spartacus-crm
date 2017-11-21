<?php
include('conn.php');
$dias = array(0, 7, 15);
$table_style  = "<style>tr:nth-child(odd) { background-color: #f8f8f8; }</style>";
$table_header = '
    <table width="95%" style="border: 1px solid black; border-spacing: 0px" align="center">
        <thead>
            <tr><td colspan="8" style="text-align: center; background-color: #ECEFF1"><strong>[titulo]</strong></td></tr>
            <tr>
                <td><strong>ID</strong></td>
                <td><strong>Renovacion</strong></td>
                <td><strong>Dias</strong></td>
                <td><strong>Poliza</strong></td>
                <td><strong>Ramo</strong></td>
                <td><strong>Producto</strong></td>
                <td><strong>UF Asegurado</strong></td>
                <td><strong>Prima Neta</strong></td>
            </tr>
        </thead>
        <tbody>';
$table_footer = "</tbody></table></body></html>";
$sql = "SELECT DISTINCT c.idejecutivo,
            e.email, CONCAT(e.nombre, ' ', e.apellidos) AS nombre
        FROM cotizacion c
            INNER JOIN usuarios e ON(c.idejecutivo = e.id)
            INNER JOIN ramos r on(c.idramo = r.id)
            LEFT JOIN productos p ON(c.idproducto = p.id)
        WHERE c.poliza <> '1'
            AND c.renovacion BETWEEN CURRENT_DATE  AND CURRENT_DATE + 15
            AND e.email<>''
        ";
$q_usr = pg_query($sql);

while($ejecutivo = pg_fetch_assoc($q_usr)) {
    $idejecutivo = $ejecutivo['idejecutivo'];
    $email = $ejecutivo['email'];
    $nombre = $ejecutivo['nombre'];
    $tables       = "";
    $valor_antes  = 0;
    foreach($dias as $dia => $valor) {
        $sql = "SELECT c.id,
                    TO_CHAR(c.renovacion,'DD/MM/YYYY') AS fecha,
                    c.renovacion - CURRENT_DATE AS dias,
                    c.poliza,
                    CONCAT(e.nombre, ' ', e.apellidos) AS ejecutivo,
                    c.idejecutivo, e.email,
                    r.descripcion AS ramo,
                    p.descripcion AS producto,
                    c.monto_asegurado, c.prima_neta
                FROM cotizacion c
                    INNER JOIN usuarios e ON(c.idejecutivo = e.id)
                    INNER JOIN ramos r on(c.idramo = r.id)
                    LEFT JOIN productos p ON(c.idproducto = p.id)
                WHERE c.poliza <> '1'
                    AND c.renovacion BETWEEN CURRENT_DATE + $valor_antes AND CURRENT_DATE + $valor
                    AND c.idejecutivo = $idejecutivo
                ORDER BY e.id, c.renovacion";
        $query = pg_query($sql);
        $valor_antes = $valor + 1;
        $sw = 0;
        if($dia == 0) {
            $titulo = "Renovaciones al dia de hoy"; 
        } else {
            $titulo = "Renovaciones a $valor dias";
        }
        $table_body = str_replace("[titulo]", $titulo, $table_header);        
        while($row = pg_fetch_assoc($query)) {
            $sw = 1;
            $table_body .= '
            <tr>
                <td><strong>'.$row['id'].'</strong></td>
                <td style="color: red">'.$row['fecha'].'</td>
                <td>'.$row['dias'].'</td>
                <td><strong>'.$row['poliza'].'</strong></td>
                <td>'.$row['ramo'].'</td>
                <td>'.$row['producto'].'</td>
                <td>'.number_format($row['monto_asegurado'],2,',','.').'</td>
                <td>'.number_format($row['prima_neta'],2,',','.').'</td>
            </tr>        
            ';
            
        }
        if($sw) {
            $tables .= $table_body.$table_footer."<br />";
        }
    }
    if($tables) {
        $tables = $table_style.$tables;
        enviarEmail($email, $nombre, $tables);
    }
}

//-- Notificaciones por correo
function enviarEmail($email, $nombre, $mensaje) {
    $subject    = "[Sistema Arcadia] Proximas renovaciones";
    $headers    = "MIME-Version: 1.0" . "\r\n";
    $headers   .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers   .= 'From: Sistema Arcadia <no-reply@spartacus.cl>' . "\r\n" . "\r\n";
    $mensaje    = "<!DOCTYPE html><html><body>Hola <strong>$nombre</strong> a continuacion tienes la lista de polizas que deben ser renovadas los proximos dias: <br /><br />$mensaje";
    $mensaje   .= "<br /><p><small>Este mensaje ha sido enviado de forma autom&aacute;tica por el sistema Arcadia y no es una cuenta de correo monitoreada</small></p><p>Saludos,<br />Equipo Spartacus</p>";
    $envio = mail($email, $subject, $mensaje, $headers);
    return $envio;
}
?>