<?php
    $email = "andyjnet@gmail.com";
    $nombre="Andy";
    $txt_premio = "Nombre del premio";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Spartacus System <no-reply@spartacus.cl>' . "\r\n" . "\r\n";
    mail($email,"Felicidades has ganado con BUD2","hola: $nombre, has ganado: $txt_premio", $headers);
    print "Listo!";
?>