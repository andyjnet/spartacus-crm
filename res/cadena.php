<?php
$html = '$("#tabla-adjuntos").html(\'
<div class="table-responsive">
  <table class="table table-striped jambo_table bulk_action" id="tbl-clientes">
    <thead>
      <tr class="headings">
      <th class="column-title text-left">Archivo  </th>
      <th class="column-title text-left">Etapa </th>
      <th class="column-title text-left">Tamaño </th>
      <th class="column-title text-left">Fecha </th>
      <th class="column-title no-link last text-center"><span class="nobr">Acci&oacute;n</span>
      </th>
      </tr>
    </thead>
    <tbody>
      <tr class="even pointer">
        <td class="text-center" colspan="5">No hay documentos adjuntos a esta cotizaci&oacute;n</td>
      </tr>
    </tbody>
  </table>
</div>\');';
$html = str_replace(array("\r", "\n", "\t"), '', $html);
	//echo '\');';
echo $html;
?>