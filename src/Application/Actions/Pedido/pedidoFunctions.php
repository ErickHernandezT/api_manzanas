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




    public function hacerPedido(String $nombreCliente, String $estadoCliente, String $ciudadCliente, String $correoCliente, String $telefonoCliente, array $manzanas)
    {
        try {
            $totalPedido = 0; // Inicializa el costo total del pedido

            // Crear un nuevo pedido
            $fechaOrdenado = date("Y-m-d");

            //Insertar el pedido y obtener el ID insertado
            $sqlCrearPedido = "INSERT INTO pedido (fechaOrdenado, total, nombreCliente, estadoCliente, ciudadCliente, correoCliente, telefonoCliente) 
            VALUES (?, ?, ?, ?, ?, ?, ?);";
            $this->DB->Ejecutar_Seguro_UTF8($sqlCrearPedido, [
                $fechaOrdenado, 0, $nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente
            ]);




            $pedidoId = $this->DB->getUltimoIdInsertado();

            // Procesar cada manzana en el pedido
            foreach ($manzanas as $manzana) {
                $idManzana = $manzana['idManzana'];
                $cantidad = $manzana['cantidad'];

                // Verificar si la manzana con el idManzana existe en la base de datos
                $sqlManzanaExistente = "SELECT precioTonelada FROM manzana WHERE id = ?";
                $manzanaExistente = $this->DB->Buscar_Seguro_UTF8($sqlManzanaExistente, [$idManzana]);

                if (empty($manzanaExistente)) {
                    return ['error' => "Tiene manzanas no existentes en su pedido"];
                }

                // Calcular el costo de esta manzana y agregarlo al costo total del pedido
                $precioTonelada = $manzanaExistente[0]['precioTonelada'];
                $costoManzana = $cantidad * $precioTonelada;
                $totalPedido += $costoManzana;

                // Agregar la manzana al pedido
                $sqlAgregarManzanaPedido = "INSERT INTO pedido_manzana (idPedido, idManzana, cantidad, subtotal) VALUES (?, ?, ?, ?)";
                $this->DB->Ejecutar_Seguro_UTF8($sqlAgregarManzanaPedido, [$pedidoId, $idManzana, $cantidad, $costoManzana]);
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
            m.id AS manzana_id, m.nombre AS manzana_nombre, pm.cantidad AS cantidad_manzana, pm.subtotal AS subtotal_manzana
        FROM pedido AS p
        LEFT JOIN pedido_manzana AS pm ON p.id = pm.idPedido
        LEFT JOIN manzana AS m ON pm.idManzana = m.id
        ORDER BY p.fechaOrdenado";

            $result = $this->DB->Buscar($sql, []);

            if (is_array($result) && count($result) > 0) {

                $pedidos = [];

                foreach ($result as $row) {
                    $pedidoId = $row['pedido_id'];

                    // Buscar el pedido actual en el arreglo de pedidos o crear uno nuevo
                    if (!isset($pedidos[$pedidoId])) {
                        $pedidos[$pedidoId] = [
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
                    }

                    // Agregar la manzana y la cantidad al pedido actual
                    $pedidos[$pedidoId]['manzanas'][] = [
                        'id' => $row['manzana_id'],
                        'nombre' => $row['manzana_nombre'],
                        'cantidad' => $row['cantidad_manzana'],
                        'subtotal' => $row['subtotal_manzana'],
                    ];
                }

                return array_values($pedidos);  // Reindexar el arreglo para obtener una lista numérica
            } else {
                return ['message' => 'No se encontró ningun pedido'];
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }



    public function obtenerPedidoPorId($pedidoId)
    {
        try {
            $sql = "SELECT p.id AS pedido_id, p.fechaOrdenado, p.total, p.nombreCliente, p.estadoCliente, p.ciudadCliente, p.correoCliente, p.telefonoCliente,
                    m.id AS manzana_id, m.nombre AS manzana_nombre, pm.cantidad AS cantidad_manzana, pm.subtotal AS subtotal_manzana
                FROM pedido AS p
                LEFT JOIN pedido_manzana AS pm ON p.id = pm.idPedido
                LEFT JOIN manzana AS m ON pm.idManzana = m.id
                WHERE p.id = ?";
    
            $result = $this->DB->Buscar_Seguro_UTF8($sql, [$pedidoId]);
    
            if (is_array($result) && count($result) > 0) {
                $pedido = null;
    
                foreach ($result as $row) {
                    // Crea el pedido si aún no existe
                    if ($pedido === null) {
                        $pedido = [
                            'id' => $row['pedido_id'],
                            'fechaOrdenado' => $row['fechaOrdenado'],
                            'total' => $row['total'],
                            'nombreCliente' => $row['nombreCliente'],
                            'estadoCliente' => $row['estadoCliente'],
                            'ciudadCliente' => $row['ciudadCliente'],
                            'correoCliente' => $row['correoCliente'],
                            'telefonoCliente' => $row['telefonoCliente'],
                            'manzanas' => [],
                        ];
                    }
    
                    // Agregar la manzana y la cantidad al pedido
                    $pedido['manzanas'][] = [
                        'id' => $row['manzana_id'],
                        'nombre' => $row['manzana_nombre'],
                        'cantidad' => $row['cantidad_manzana'],
                        'subtotal' => $row['subtotal_manzana'],
                    ];
                }
    
                return $pedido;
            } else {
                return ['message' => 'No se encontró el pedido con el ID proporcionado'];
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    





    //Función para actualizar el pedido
    public function actualizarPedido(int $idPedido, String $nombreCliente, String $estadoCliente, String $ciudadCliente, String $correoCliente, String $telefonoCliente, array $manzanas)
    {
        try {
            $totalPedido = 0; // Inicializa el costo total del pedido

            // Verificar si el pedido con el idPedido existe en la base de datos
            $sqlPedidoExistente = "SELECT * FROM pedido WHERE id = ?";
            $pedidoExistente = $this->DB->Buscar_Seguro_UTF8($sqlPedidoExistente, [$idPedido]);

            if (empty($pedidoExistente)) {
                return ['error' => "Su pedido no se ha encontrado"];
            }

            // Eliminar las manzanas del pedido existente
            $sqlEliminarManzanasPedido = "DELETE FROM pedido_manzana WHERE idPedido = ?";
            $this->DB->Ejecutar_Seguro_UTF8($sqlEliminarManzanasPedido, [$idPedido]);

            // Procesar cada manzana en el pedido actualizado
            foreach ($manzanas as $manzana) {
                $idManzana = $manzana['idManzana'];
                $cantidad = $manzana['cantidad'];

                // Verificar si la manzana con el idManzana existe en la base de datos
                $sqlManzanaExistente = "SELECT precioTonelada FROM manzana WHERE id = ?";
                $manzanaExistente = $this->DB->Buscar_Seguro_UTF8($sqlManzanaExistente, [$idManzana]);

                if (empty($manzanaExistente)) {
                    return ['error' => "Una de las manzanas no se encuentra."];
                }

                // Calcular el costo de esta manzana y agregarlo al costo total del pedido
                $precioTonelada = $manzanaExistente[0]['precioTonelada'];
                $costoManzana = $cantidad * $precioTonelada;
                $totalPedido += $costoManzana;

                // Agregar la manzana al pedido
                $sqlAgregarManzanaPedido = "INSERT INTO pedido_manzana (idPedido, idManzana, cantidad, subtotal) VALUES (?, ?, ?, ?)";
                $this->DB->Ejecutar_Seguro_UTF8($sqlAgregarManzanaPedido, [$idPedido, $idManzana, $cantidad, $costoManzana]);
            }

            // Actualizar el costo total del pedido en la tabla 'pedido'
            $sqlActualizarTotalPedido = "UPDATE pedido SET nombreCliente = ?, estadoCliente = ?, ciudadCliente = ?,
         correoCliente = ?, telefonoCliente = ?, total = ? WHERE id = ?";
            $this->DB->Ejecutar_Seguro_UTF8($sqlActualizarTotalPedido, [
                $nombreCliente, $estadoCliente, $ciudadCliente,
                $correoCliente, $telefonoCliente, $totalPedido, $idPedido
            ]);

            return ['message' => "Pedido actualizado con éxito"];
        } catch (Exception $e) {
            return ['error' => "No se ha podido actualizar el pedido"];
        }
    }


    //Función para eliminar un pedido
    public function eliminarPedido(int $idPedido)
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql = "DELETE * FROM pedido WHERE id = ?;";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$idPedido]);
        // Verificamos si la eliminación fue exitosa (código 200)
        return ($statement == '200') ? true : false;;
    }



    // //Función para liberar el pedido y que se considere como venta
    // public function liberarPedido(int $idPedido)
    // {
    //     try {
    //         // Obtener los datos del pedido a cerrar
    //         $sqlObtenerPedido = "SELECT * FROM pedido WHERE id = ?";
    //         $pedido = $this->DB->Buscar_Seguro_UTF8($sqlObtenerPedido, [$idPedido]);

    //         if (empty($pedido)) {
    //             return ['error' => "Lo lamentamos su pedido no se encuentra"];
    //         }

    //         // Crear una nueva venta con los datos del pedido y obtener el ID insertado
    //         $fechaLiberado = date("Y-m-d");
    //         $total = $pedido[0]['total'];
    //         $nombreCliente = $pedido[0]['nombreCliente'];
    //         $estadoCliente = $pedido[0]['estadoCliente'];
    //         $ciudadCliente = $pedido[0]['ciudadCliente'];
    //         $correoCliente = $pedido[0]['correoCliente'];
    //         $telefonoCliente = $pedido[0]['telefonoCliente'];

    //         $sqlCrearVenta = "INSERT INTO venta (fechaLiberado, total, nombreCliente, estadoCliente, ciudadCliente, correoCliente, telefonoCliente) 
    //         VALUES (?, ?, ?, ?, ?, ?, ?);";
    //         $ventaIdQuery = $this->DB->Ejecutar_Seguro_UTF8($sqlCrearVenta, [
    //             $fechaLiberado, $total, $nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente
    //         ]);
    //         $ventaId =  $this->DB->getUltimoIdInsertado();
    //         // Transferir los registros de pedido_manzana a venta_manzana
    //         $sqlTransferirManzanas = "INSERT INTO venta_manzana (idVenta, idManzana, cantidad, subtotal) 
    //         SELECT ?, idManzana, cantidad, subtotal FROM pedido_manzana WHERE idPedido = ?";
    //         $this->DB->Ejecutar_Seguro_UTF8($sqlTransferirManzanas, [$ventaId, $idPedido]);

    //         // Eliminar el pedido
    //         $sqlEliminarPedido = "DELETE FROM pedido WHERE id = ?";
    //         $this->DB->Ejecutar_Seguro_UTF8($sqlEliminarPedido, [$idPedido]);

    //         return ['message' => "Pedido liberado"];
    //     } catch (Exception $e) {
    //         return ['error' => "Error al liberar pedido"];
    //     }
    // }




    public function liberarPedido(int $idPedido)
{
    try {
        // Obtener los datos del pedido a cerrar
        $sqlObtenerPedido = "SELECT * FROM pedido WHERE id = ?";
        $pedido = $this->DB->Buscar_Seguro_UTF8($sqlObtenerPedido, [$idPedido]);

        if (empty($pedido)) {
            return ['error' => "Lo lamentamos, su pedido no se encuentra"];
        }

        // Verificar el stock de cada manzana en el pedido
        $sqlVerificarStock = "SELECT pm.idManzana, m.stock, SUM(pm.cantidad) as total_cantidad
            FROM pedido_manzana pm
            INNER JOIN manzana m ON pm.idManzana = m.id
            WHERE pm.idPedido = ?
            GROUP BY pm.idManzana";
        $resultVerificarStock = $this->DB->Buscar_Seguro_UTF8($sqlVerificarStock, [$idPedido]);

        foreach ($resultVerificarStock as $row) {
            $idManzana = $row['idManzana'];
            $stockDisponible = $row['stock'];
            $cantidadPedido = $row['total_cantidad'];

            if ($stockDisponible < $cantidadPedido) {
                return ['error' => "No hay suficiente stock de la manzana con ID $idManzana para satisfacer el pedido"];
            }
        }

        // Actualizar el stock de cada manzana
        foreach ($resultVerificarStock as $row) {
            $idManzana = $row['idManzana'];
            $cantidadPedido = $row['total_cantidad'];

            $sqlActualizarStock = "UPDATE manzana SET stock = stock - ? WHERE id = ?";
            $this->DB->Ejecutar_Seguro_UTF8($sqlActualizarStock, [$cantidadPedido, $idManzana]);
        }

        // Crear una nueva venta con los datos del pedido y obtener el ID insertado
        $fechaLiberado = date("Y-m-d");
        $total = $pedido[0]['total'];
        $nombreCliente = $pedido[0]['nombreCliente'];
        $estadoCliente = $pedido[0]['estadoCliente'];
        $ciudadCliente = $pedido[0]['ciudadCliente'];
        $correoCliente = $pedido[0]['correoCliente'];
        $telefonoCliente = $pedido[0]['telefonoCliente'];

        $sqlCrearVenta = "INSERT INTO venta (fechaLiberado, total, nombreCliente, estadoCliente, ciudadCliente, correoCliente, telefonoCliente) 
        VALUES (?, ?, ?, ?, ?, ?, ?);";
        $ventaIdQuery = $this->DB->Ejecutar_Seguro_UTF8($sqlCrearVenta, [
            $fechaLiberado, $total, $nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente
        ]);
        $ventaId =  $this->DB->getUltimoIdInsertado();

        // Transferir los registros de pedido_manzana a venta_manzana
        $sqlTransferirManzanas = "INSERT INTO venta_manzana (idVenta, idManzana, cantidad, subtotal) 
        SELECT ?, idManzana, cantidad, subtotal FROM pedido_manzana WHERE idPedido = ?";
        $this->DB->Ejecutar_Seguro_UTF8($sqlTransferirManzanas, [$ventaId, $idPedido]);

        // Eliminar el pedido
        $sqlEliminarPedido = "DELETE FROM pedido WHERE id = ?";
        $this->DB->Ejecutar_Seguro_UTF8($sqlEliminarPedido, [$idPedido]);

        return ['message' => "Pedido liberado"];
    } catch (Exception $e) {
        return ['error' => "Error al liberar pedido"];
    }
}

}
