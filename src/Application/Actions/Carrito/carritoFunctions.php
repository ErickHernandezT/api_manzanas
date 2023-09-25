<?php

namespace App\Application\Actions\Carrito;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;
use Exception;
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




    public function agregarAlCarrito(int $idUsuario, int $idManzana, int $cantidad)
{
    try {
        // Verificar si la manzana con el idManzana existe en la base de datos
        $sqlManzanaExistente = "SELECT precio FROM manzana WHERE id = ?";
        $manzanaExistente = $this->DB->Buscar_Seguro_UTF8($sqlManzanaExistente, [$idManzana]);

        if (empty($manzanaExistente)) {
            return ['error' => "La manzana con ID $idManzana no existe en la base de datos."];
        }

        // Verificar si ya existe un carrito para el usuario
        $sqlCarritoExistente = "SELECT id, total FROM carrito WHERE idUsuario = ?";
        $carritoExistente = $this->DB->Buscar_Seguro_UTF8($sqlCarritoExistente, [$idUsuario]);

        if (empty($carritoExistente)) {
            // Si no existe un carrito para el usuario, crea uno nuevo
            $sqlCrearCarrito = "INSERT INTO carrito (total, idUsuario) VALUES (?, ?)";
            $this->DB->Ejecutar_Seguro_UTF8($sqlCrearCarrito, [0, $idUsuario]);
            
            // Obtener el ID del carrito recién creado
            $sqlIdCarrito = "SELECT id FROM carrito WHERE idUsuario = ?";
            $carritoIdQuery = $this->DB->Buscar_Seguro_UTF8($sqlIdCarrito, [$idUsuario]);
            $carritoId = $carritoIdQuery[0]['id'];
            $totalCarritoExistente = 0;
        } else {
            // Obtener el ID del carrito existente
            $carritoId = $carritoExistente[0]['id'];
            $totalCarritoExistente = $carritoExistente[0]['total'];
        }

        $totalCarrito = $totalCarritoExistente; // Inicializa el total del carrito con el total existente

        // Verificar si ya existe una entrada de esta manzana en el carrito
        $sqlEntradaExistente = "SELECT id, cantidad FROM carrito_manzana WHERE idCarrito = ? AND idManzana = ?";
        $entradaExistente = $this->DB->Buscar_Seguro_UTF8($sqlEntradaExistente, [$carritoId, $idManzana]);

        if (!empty($entradaExistente)) {
            // Si existe una entrada, actualiza la cantidad
            $entradaId = $entradaExistente[0]['id'];
            $cantidadExistente = $entradaExistente[0]['cantidad'];
            $cantidadNueva = $cantidadExistente + $cantidad;
            $sqlActualizarCarritoManzana = "UPDATE carrito_manzana SET cantidad = ? WHERE id = ?";
            $this->DB->Ejecutar_Seguro_UTF8($sqlActualizarCarritoManzana, [$cantidadNueva, $entradaId]);
        } else {
            // Si no existe una entrada, crea una nueva
            $sqlInsertarCarritoManzana = "INSERT INTO carrito_manzana (idCarrito, idManzana, cantidad) VALUES (?, ?, ?)";
            $this->DB->Ejecutar_Seguro_UTF8($sqlInsertarCarritoManzana, [$carritoId, $idManzana, $cantidad]);
        }

        // Obtener el precio de la manzana desde la base de datos
        $sqlPrecioManzana = "SELECT precio FROM manzana WHERE id = ?";
        $precioManzana = $this->DB->Buscar_Seguro_UTF8($sqlPrecioManzana, [$idManzana]);
        if (!empty($precioManzana)) {
            $precioManzana = $precioManzana[0]['precio'];

            // Calcular el costo de esta manzana y agregarlo al total del carrito
            $costoManzana = $cantidad * $precioManzana;
            $totalCarrito += $costoManzana;
        } else {
            return ['error' => "No se pudo obtener el precio de la manzana con ID $idManzana."];
        }

        // Actualizar el total del carrito en la tabla 'carrito'
        $sqlActualizarCarrito = "UPDATE carrito SET total = ? WHERE id = ?";
        $this->DB->Ejecutar_Seguro_UTF8($sqlActualizarCarrito, [$totalCarrito, $carritoId]);

        return ['message' => "Manzana agregada al carrito con éxito"];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}


   




    
}
