<?php
namespace App\Application\Settings;
//esta clase establece las variable para la conexion a la Base de Datos
error_reporting( error_reporting() & ~E_NOTICE );
date_default_timezone_set('America/Mexico_City');

class config{
    const SERVERNAME = 'localhost';
    const USERNAME = 'root';
    const PASSWORD = '1020';
    const DATABASE = 'cpmt_inventario';
}
?>
