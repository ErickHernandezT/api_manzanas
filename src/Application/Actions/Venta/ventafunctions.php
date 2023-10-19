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



    




public function listaVentas($fechaInicial, $fechaFinal){
    try {
        $sql = "SELECT v.id AS venta_id, v.fechaLiberado, v.total, v.nombreCliente, v.estadoCliente, v.ciudadCliente, v.correoCliente, v.telefonoCliente,
                m.id AS manzana_id, m.nombre AS manzana_nombre, vm.cantidad AS cantidad_manzana, vm.subtotal AS subtotal_manzana
            FROM venta AS v
            LEFT JOIN venta_manzana AS vm ON v.id = vm.idVenta
            LEFT JOIN manzana AS m ON vm.idManzana = m.id
            WHERE v.fechaLiberado BETWEEN ? AND ?
            ORDER BY v.fechaLiberado";

        $result = $this->DB->Buscar_Seguro_UTF8($sql, [$fechaInicial, $fechaFinal]);

        $ventas = [];
        $currentVenta = null;

        foreach ($result as $row) {
            $ventaId = $row['venta_id'];

            if ($currentVenta === null || $ventaId !== $currentVenta['id']) {
                // Nueva venta
                $currentVenta = [
                    'id' => $ventaId,
                    'fechaLiberado' => $row['fechaLiberado'],
                    'total' => $row['total'],
                    'nombreCliente' => $row['nombreCliente'],
                    'estadoCliente' => $row['estadoCliente'],
                    'ciudadCliente' => $row['ciudadCliente'],
                    'correoCliente' => $row['correoCliente'],
                    'telefonoCliente' => $row['telefonoCliente'],
                    'manzanas' => [],
                ];
                $ventas[] = $currentVenta;
            }

            // Agregar la manzana y la cantidad a la venta actual
            $currentVenta['manzanas'][] = [
                'id' => $row['manzana_id'],
                'nombre' => $row['manzana_nombre'],
                'cantidad' => $row['cantidad_manzana'],
                'subtotal' => $row['subtotal_manzana'],
            ];
        }

        return $ventas;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}


}

