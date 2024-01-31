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
    // const SERVERNAME = 'localhost';
    // const USERNAME = 'id21551630_root';
    // const PASSWORD = '+Criomante125';
    // const DATABASE = 'id21551630_cpmt_manzanas';
}
?>
