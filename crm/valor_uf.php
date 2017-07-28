<?php
$valor = 0;
include_once('../includes/conn.php');
$sql = "SELECT v.valor
        FROM monedas m
          INNER JOIN monedas_valor v ON(v.idmoneda = m.id)
        WHERE UPPER(m.simbolo) = 'UF'
        AND v.fecha = CURRENT_DATE";
$query = pg_query($conn, $sql);
if($row = pg_fetch_assoc($query)) {
  $valor = $row['valor'];
} else {
  /* Buscamos el valor en los indicadores diarios del banco central */
  $contenido = file_get_contents('http://si3.bcentral.cl/Indicadoressiete/secure/Indicadoresdiarios.aspx');
  $ini = strpos($contenido, 'lblValor1_1') + 13;
  $fin = strpos($contenido, "</label>", $ini);
  $valor = substr($contenido, $ini, $fin - $ini);
  $valor = str_replace(".","",$valor);
  $valor = str_replace(",",".",$valor);
  $sql = "SELECT id AS idmoneda,
            (SELECT id FROM monedas WHERE principal = 1 LIMIT 1) AS idmoneda_valor
          FROM monedas
          WHERE UPPER(simbolo) = 'UF'";
  $query = pg_query($sql);
  $row = pg_fetch_assoc($query);
  $idmoneda = $row['idmoneda'];
  $idmoneda_valor = $row['idmoneda_valor'];
  $sql = "INSERT INTO monedas_valor(idmoneda, idmoneda_valor, fecha, valor)
          VALUES($idmoneda, $idmoneda_valor, CURRENT_DATE, $valor)";
  pg_query($conn, $sql);
}
$valor_uf = $valor;
?>