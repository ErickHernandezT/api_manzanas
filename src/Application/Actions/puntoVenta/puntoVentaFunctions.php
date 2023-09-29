<?php

namespace App\Application\Actions\puntoVenta;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class puntoVentaFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }

    public function listaPuntosVenta()
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql2 = "SELECT id, nombre, foto, latitud, longitud, estatus, horario FROM punto_venta";
    
        $statement = $this->DB->Buscar($sql2, []);
    
        if (is_array($statement) && count($statement) > 0) {
            return $statement;
        } else {
            return ['message' =>  $statement];
        }
    }



    public function buscarPuntoVentaPorId(int $id)
    {
        // Query SQL para buscar una manzana por su ID
        $sql = "SELECT id, nombre, foto, latitud, longitud, estatus, horario FROM punto_venta WHERE id = ?";
    
        // Ejecutamos la consulta
        $result = $this->DB->Buscar_Seguro_UTF8($sql, [$id]);
    
        if (is_array($result) && count($result) > 0) {
            return $result[0]; // Devuelve la primera fila que coincide con el ID
        } else {
            return ['message' => 'No se encontró ningun punto de venta con el ID proporcionado'];
        }
    }



    public function ingresarPuntoVenta(String $nombre, String $foto, String $latitud, String $longitud, int $estatus, String $horario)
    {
        // Se usa left join para que también muestre los productos que no tengan
        $sql2 = "INSERT INTO punto_venta (nombre, foto, latitud, longitud, estatus, horario) VALUES (?, ?, ?, ?, ?, ?)";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql2, [$nombre, $foto, $latitud, $longitud, $estatus, $horario]);
        return ($statement == '200') ? true : false;
    }



    public function actualizarPuntoVenta(int $id, String $nombre, String $foto, String $latitud, String $longitud, int $estatus, String $horario)
    {
        // Query SQL para actualizar los datos en la tabla manzana
        $sql = "UPDATE punto_venta SET nombre = ?, foto = ?, latitud = ?, longitud = ?, estatus = ?, horario = ? WHERE id = ?";

        // Agregamos el ID como último valor en el array de parámetros
        $parametros = [$nombre, $foto, $latitud, $longitud, $estatus, $horario, $id];

        // Ejecutamos la consulta
        $resultado = $this->DB->Ejecutar_Seguro_UTF8($sql, $parametros);

        // Verificamos si la actualización fue exitosa (código 200)
        return ($resultado === '200');
    }



    public function eliminarPuntoVenta(int $id)
    {
        // Query SQL para eliminar una entrada de la tabla manzana por ID
        $sql = "DELETE FROM punto_venta WHERE id = ?";

        // Ejecutamos la consulta
        $resultado = $this->DB->Ejecutar_Seguro_UTF8($sql, [$id]);

        // Verificamos si la eliminación fue exitosa (código 200)
        return ($resultado === '200');
    }

}