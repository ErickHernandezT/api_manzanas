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



    public function ingresarManzana(String $nombre, String $foto, String $nivelMadurez, String $descripcion, Int $estatus, float $precioKilo, float $precioCaja, float $precioTonelada, Int $stock, Int $categoria)
    {
        // Se usa left join para que también muestre los productos que no tengan
        $sql2 = "INSERT INTO manzana (nombre, foto, nivelMadurez, descripcion, estatus, precioKilo, precioCaja, precioTonelada, stock, idCategoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql2, [$nombre, $foto, $nivelMadurez, $descripcion, $estatus, $precioKilo, $precioCaja, $precioTonelada, $stock, $categoria]);
        return ($statement == '200') ? true : false;
    }

    public function listaManzanas()
    {
        $sql = "SELECT m.id, m.nombre, m.nivelMadurez, m.descripcion, m.estatus, m.precioKilo, m.precioCaja, m.precioTonelada, m.stock, m.foto, c.nombre AS categoria_nombre
            FROM manzana AS m
            LEFT JOIN categoria AS c ON m.idCategoria = c.id";

        $statement = $this->DB->Buscar($sql, []);

        if (is_array($statement) && count($statement) > 0) {
            return $statement;
        } else {
            return ['message' => "Error al mostrar las manzanas"];
        }
    }


    public function listaManzanasSencilla()
    {
        $sql = "SELECT id, nombre FROM manzana";

        $statement = $this->DB->Buscar($sql, []);

        if (is_array($statement) && count($statement) > 0) {
            return $statement;
        } else {
            return ['message' => "Error al mostrar las manzanas"];
        }
    }
    



    public function actualizarManzana(int $id, String $nombre, String $foto, String $nivelMadurez, String $descripcion, Int $estatus, float $precioKilo, float $precioCaja, float $precioTonelada, Int $stock, Int $categoria)
    {
        // Query SQL para actualizar los datos en la tabla manzana
        $sql = "UPDATE manzana SET nombre = ?, foto = ?, nivelMadurez = ?, descripcion = ?, estatus = ?, precioKilo = ?, precioCaja = ?, precioTonelada = ?, stock = ?, idCategoria = ? WHERE id = ?";

        // Agregamos el ID como último valor en el array de parámetros
        $parametros = [$nombre, $foto, $nivelMadurez, $descripcion, $estatus, $precioKilo, $precioCaja, $precioTonelada, $stock, $categoria, $id];

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



    public function buscarManzanaPorId(int $id)
    {
        // Query SQL para buscar una manzana por su ID
        $sql = "SELECT m.id, m.nombre, m.nivelMadurez, m.descripcion, m.estatus, m.precioKilo, m.precioCaja, m.precioTonelada, m.stock, m.foto, c.nombre AS categoria_nombre
        FROM manzana AS m
        LEFT JOIN categoria AS c ON m.idCategoria = c.id
        WHERE m.id = ?";

        // Ejecutamos la consulta
        $result = $this->DB->Buscar_Seguro_UTF8($sql, [$id]);

        if (is_array($result) && count($result) > 0) {
            return $result[0]; // Devuelve la primera fila que coincide con el ID
        } else {
            return ['message' => 'Error al mostrar la manzana seleccionada'];
        }
    }
}
