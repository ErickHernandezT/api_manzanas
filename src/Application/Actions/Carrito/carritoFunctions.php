<?php

namespace App\Application\Actions\Carrito;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class carritoFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }



    public function mostrarCarrito(int $idUsuario)
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql = "SELECT
        c.*,
        m.nombre AS nombre_manzana, m.foto AS foto,
        cm.cantidad AS cantidad_en_carrito
    FROM
        carrito c
    INNER JOIN
        carrito_manzana cm ON c.id = cm.idCarrito
    INNER JOIN
        manzana m ON cm.idManzana = m.id
    WHERE
        c.idUsuario = ?; -- Cambia el 1 por el ID del carrito que deseas consultar
    ";

        $statement = $this->DB->Buscar($sql, [$idUsuario]);

        if (is_array($statement) && count($statement) > 0) {
            // Codifica la imagen en formato base64 y agrégala al resultado
            foreach ($statement as &$row) {
                if ($row['foto'] !== null) {
                    $row['foto'] = base64_encode($row['foto']);
                }
            }
            return $statement;
        } else {
            return ['message' =>  "Error al mostrar el carrito"];
        }
    }




    public function eliminarCarrito(int $idUsuario)
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql = "DELETE FROM carrito WHERE idUsuario = ?;";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$idUsuario]);
        // Verificamos si la eliminación fue exitosa (código 200)
        return ($statement == '200') ? true : false;;
    }



    
}
