<?php

namespace App\Application\Actions\Venta;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;
use Exception;
use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class ventaFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }



    public function cerrarPedido(int $idPedido)
{
    try {
        // Obtener los datos del pedido a cerrar
        $sqlObtenerPedido = "SELECT * FROM pedido WHERE id = ?";
        $pedido = $this->DB->Buscar_Seguro_UTF8($sqlObtenerPedido, [$idPedido]);

        if (empty($pedido)) {
            return ['error' => "El pedido con ID $idPedido no existe en la base de datos."];
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
            VALUES (?, ?, ?, ?, ?, ?, ?); SELECT LAST_INSERT_ID() as id;";
        $ventaIdQuery = $this->DB->Buscar_Seguro_UTF8($sqlCrearVenta, [
            $fechaLiberado, $total, $nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente
        ]);
        $ventaId = $ventaIdQuery[0]['id'];

        // Transferir los registros de pedido_manzana a venta_manzana
        $sqlTransferirManzanas = "INSERT INTO venta_manzana (idVenta, idManzana, cantidad) 
            SELECT ?, idManzana, cantidad FROM pedido_manzana WHERE idPedido = ?";
        $this->DB->Ejecutar_Seguro_UTF8($sqlTransferirManzanas, [$ventaId, $idPedido]);

        // Eliminar el pedido
        $sqlEliminarPedido = "DELETE FROM pedido WHERE id = ?";
        $this->DB->Ejecutar_Seguro_UTF8($sqlEliminarPedido, [$idPedido]);

        return ['message' => "Pedido cerrado y transferido a venta con éxito"];
    } catch (Exception $e) {
        return ['error' => "Error al liberar pedido"];
    }
}

}

