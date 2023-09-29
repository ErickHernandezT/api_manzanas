<?php

namespace App\Application\Actions\Manzana;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class manzanaFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }


    //Método para listar manzanas
    public function listaManzanas()
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql2 = "SELECT id, nombre, nivelMadurez, descripcion, estatus, precio, stock, foto FROM manzana";

        $statement = $this->DB->Buscar($sql2, []);

        if (is_array($statement) && count($statement) > 0) {

            return $statement;
        } else {
            return ['message' =>  "Error al mostrar las manzanas"];
        }
    }


    public function buscarManzanaPorId(int $id)
    {
        // Query SQL para buscar una manzana por su ID
        $sql = "SELECT id, nombre, foto, nivelMadurez, descripcion, estatus, precio, stock FROM manzana WHERE id = ?";

        // Ejecutamos la consulta
        $result = $this->DB->Buscar_Seguro_UTF8($sql, [$id]);

        if (is_array($result) && count($result) > 0) {
            return $result[0]; // Devuelve la primera fila que coincide con el ID
        } else {
            return ['message' => 'Error al mostrar la manzana seleccionada'];
        }
    }






    public function ingresarManzanas(String $nombre, String $foto, String $nivelMadurez, String $descripcion, Int $estatus, float $precio, Int $stock)
    {
        // Se usa left join para que también muestre los productos que no tengan
        $sql2 = "INSERT INTO manzana (nombre, foto, nivelMadurez, descripcion, estatus, precio, stock) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql2, [$nombre, $foto, $nivelMadurez, $descripcion, $estatus, $precio, $stock]);
        return ($statement == '200') ? true : false;
    }




    public function actualizarManzanas(int $id, String $nombre, String $foto, String $nivelMadurez, String $descripcion, Int $estatus, float $precio, Int $stock)
    {
        // Query SQL para actualizar los datos en la tabla manzana
        $sql = "UPDATE manzana SET nombre = ?, foto = ?, nivelMadurez = ?, descripcion = ?, estatus = ?, precio = ?, stock = ? WHERE id = ?";

        // Agregamos el ID como último valor en el array de parámetros
        $parametros = [$nombre, $foto, $nivelMadurez, $descripcion, $estatus, $precio, $stock, $id];

        // Ejecutamos la consulta
        $resultado = $this->DB->Ejecutar_Seguro_UTF8($sql, $parametros);

        // Verificamos si la actualización fue exitosa (código 200)
        return ($resultado === '200');
    }


    public function eliminarManzana(int $id)
    {
        // Query SQL para eliminar una entrada de la tabla manzana por ID
        $sql = "DELETE FROM manzana WHERE id = ?";

        // Ejecutamos la consulta
        $resultado = $this->DB->Ejecutar_Seguro_UTF8($sql, [$id]);

        // Verificamos si la eliminación fue exitosa (código 200)
        return ($resultado === '200');
    }
}
