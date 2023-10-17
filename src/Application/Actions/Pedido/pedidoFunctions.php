<?php

namespace App\Application\Actions\Pedido;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;
use Exception;
use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class pedidoFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }



    public function mostrarPedido(int $idUsuario)
    {



    }




    public function eliminarPedido(int $idPedido)
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql = "DELETE FROM pedido WHERE id = ?;";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$idPedido]);
        // Verificamos si la eliminación fue exitosa (código 200)
        return ($statement == '200') ? true : false;;
    }



//     public function hacerPedido(int $nombreCliente, int $estadoCliente, int $ciudadCliente, int $correoCliente,
//     int $telefonoCliente, int $idManzana, int $cantidad)
// {
//     try {
//         // Verificar si la manzana con el idManzana existe en la base de datos
//         $sqlManzanaExistente = "SELECT precioKilo FROM manzana WHERE id = ?";
//         $manzanaExistente = $this->DB->Buscar_Seguro_UTF8($sqlManzanaExistente, [$idManzana]);

//         if (empty($manzanaExistente)) {
//             return ['error' => "La manzana con ID $idManzana no existe en la base de datos."];
//         }

//         // Calcular el costo de esta manzana
//         $precioKilo = $manzanaExistente[0]['precioKilo'];
//         $costoManzana = $cantidad * $precioKilo;

//         // Crear un nuevo pedido
//         $fechaOrdenado = date("Y-m-d");
       

//         $sqlCrearPedido = "INSERT INTO pedido (fechaOrdenado, total, nombreCliente, estadoCliente, ciudadCliente, correoCliente, telefonoCliente) VALUES (?, ?, ?, ?, ?, ?, ?)";
//         $this->DB->Ejecutar_Seguro_UTF8($sqlCrearPedido, [$fechaOrdenado, $costoManzana, $nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente]);

//         // Obtener el ID del pedido recién creado
//         $pedidoId = $this->DB->getLastInsertId();

//         // Agregar la manzana al pedido
//         $sqlAgregarManzanaPedido = "INSERT INTO pedido_manzana (idPedido, idManzana, cantidad) VALUES (?, ?, ?)";
//         $this->DB->Ejecutar_Seguro_UTF8($sqlAgregarManzanaPedido, [$pedidoId, $idManzana, $cantidad]);

//         return ['message' => "Pedido realizado con éxito"];
//     } catch (Exception $e) {
//         return ['error' => $e->getMessage()];
//     }
// }

public function hacerPedido(int $nombreCliente, int $estadoCliente, int $ciudadCliente, int $correoCliente, int $telefonoCliente, array $manzanas)
{
    try {
        $totalPedido = 0; // Inicializa el costo total del pedido

        // Crear un nuevo pedido
        $fechaOrdenado = date("Y-m-d");

        // Insertar el pedido y obtener el ID insertado
        $sqlCrearPedido = "INSERT INTO pedido (fechaOrdenado, total, nombreCliente, estadoCliente, ciudadCliente, correoCliente, telefonoCliente) 
            VALUES (?, ?, ?, ?, ?, ?, ?); SELECT LAST_INSERT_ID() as id;";
        $pedidoIdQuery = $this->DB->Buscar_Seguro_UTF8($sqlCrearPedido, [
            $fechaOrdenado, 0, $nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente
        ]);
        $pedidoId = $pedidoIdQuery[0]['id'];

        // Procesar cada manzana en el pedido
        foreach ($manzanas as $manzana) {
            $idManzana = $manzana['idManzana'];
            $cantidad = $manzana['cantidad'];

            // Verificar si la manzana con el idManzana existe en la base de datos
            $sqlManzanaExistente = "SELECT precioKilo FROM manzana WHERE id = ?";
            $manzanaExistente = $this->DB->Buscar_Seguro_UTF8($sqlManzanaExistente, [$idManzana]);

            if (empty($manzanaExistente)) {
                return ['error' => "La manzana con ID $idManzana no existe en la base de datos."];
            }

            // Calcular el costo de esta manzana y agregarlo al costo total del pedido
            $precioTonelada = $manzanaExistente[0]['precioTonelada'];
            $costoManzana = $cantidad * $precioTonelada;
            $totalPedido += $costoManzana;

            // Agregar la manzana al pedido
            $sqlAgregarManzanaPedido = "INSERT INTO pedido_manzana (idPedido, idManzana, cantidad) VALUES (?, ?, ?)";
            $this->DB->Ejecutar_Seguro_UTF8($sqlAgregarManzanaPedido, [$pedidoId, $idManzana, $cantidad]);
        }

        // Actualizar el costo total del pedido en la tabla 'pedido'
        $sqlActualizarTotalPedido = "UPDATE pedido SET total = ? WHERE id = ?";
        $this->DB->Ejecutar_Seguro_UTF8($sqlActualizarTotalPedido, [$totalPedido, $pedidoId]);

        return ['message' => "Pedido realizado con éxito"];
    } catch (Exception $e) {
        return ['error' => "No se ha podido realizar tu pedido"];
    }
}


public function listaPedidos()
{
    try {
        $sql = "SELECT p.id AS pedido_id, p.fechaOrdenado, p.total, p.nombreCliente, p.estadoCliente, p.ciudadCliente, p.correoCliente, p.telefonoCliente,
                m.id AS manzana_id, m.nombre AS manzana_nombre, pm.cantidad AS cantidad_manzana
            FROM pedido AS p
            LEFT JOIN pedido_manzana AS pm ON p.id = pm.idPedido
            LEFT JOIN manzana AS m ON pm.idManzana = m.id
            ORDER BY p.fechaOrdenado";

        $result = $this->DB->Buscar_Seguro_UTF8($sql, []);

        $pedidos = [];
        $currentPedido = null;

        foreach ($result as $row) {
            $pedidoId = $row['pedido_id'];

            if ($currentPedido === null || $pedidoId !== $currentPedido['id']) {
                // Nuevo pedido
                $currentPedido = [
                    'id' => $pedidoId,
                    'fechaOrdenado' => $row['fechaOrdenado'],
                    'total' => $row['total'],
                    'nombreCliente' => $row['nombreCliente'],
                    'estadoCliente' => $row['estadoCliente'],
                    'ciudadCliente' => $row['ciudadCliente'],
                    'correoCliente' => $row['correoCliente'],
                    'telefonoCliente' => $row['telefonoCliente'],
                    'manzanas' => [],
                ];
                $pedidos[] = $currentPedido;
            }

            // Agregar la manzana y la cantidad al pedido actual
            $currentPedido['manzanas'][] = [
                'id' => $row['manzana_id'],
                'nombre' => $row['manzana_nombre'],
                'cantidad' => $row['cantidad_manzana'],
            ];
        }

        return $pedidos;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}






    

}