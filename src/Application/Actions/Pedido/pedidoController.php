<?php

namespace App\Application\Actions\Pedido;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Pedido\pedidoFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class pedidoController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new pedidoFunctions();
    }




    public function validarHacerPedido($request, $response, $args)
{
    $params = (array)$request->getParsedBody();

    $nombreCliente = (isset($params['nombreCliente'])) ? strip_tags($params['nombreCliente']) : '';
    $estadoCliente = (isset($params['estadoCliente'])) ? strip_tags($params['estadoCliente']) : '';
    $ciudadCliente = (isset($params['ciudadCliente'])) ? strip_tags($params['ciudadCliente']) : '';
    $correoCliente = (isset($params['correoCliente'])) ? strip_tags($params['correoCliente']) : '';
    $telefonoCliente = (isset($params['telefonoCliente'])) ? strip_tags($params['telefonoCliente']) : '';

    $manzanas = (isset($params['manzanas'])) ? $params['manzanas'] : [];

    if ($nombreCliente != '' && $estadoCliente != '' && $ciudadCliente != '' && $correoCliente != '' && $telefonoCliente != '' && !empty($manzanas)) {
        $manzanasArray = [];

        foreach ($manzanas as $manzana) {
            $idManzana = (isset($manzana['idManzana'])) ? (int)strip_tags($manzana['idManzana']) : 0;
            $cantidad = (isset($manzana['cantidad'])) ? (int)strip_tags($manzana['cantidad']) : 0;

            if ($idManzana > 0 && $cantidad > 0) {
                $manzanasArray[] = [
                    'idManzana' => $idManzana,
                    'cantidad' => $cantidad
                ];
            }
        }

        if (!empty($manzanasArray)) {
            // Llama a la función para hacer el pedido
            $mensaje = $this->funciones->hacerPedido($nombreCliente, $estadoCliente, $ciudadCliente, $correoCliente, $telefonoCliente, $manzanasArray);

            if ($mensaje && isset($mensaje['message'])) {
                $code = 200;
            } else {
                $code = 404;
            }
        } else {
            $code = 400;
            $mensaje = ['message' => 'Datos incorrectos o vacíos en las manzanas'];
        }
    } else {
        $code = 400;
        $mensaje = ['message' => 'Datos incorrectos o vacíos en el pedido'];
    }

    return $this->response($code, $mensaje, $response);
}


public function validarListaPedidos($request, $response, $args)
{
    

        $mensaje = $this->funciones->listaPedidos();

        if ($mensaje) {
            $code = 200;
        } else {
            $code = 404;
            $mensaje = ['message' => 'Error al cargar los pedidos'];
        }
   

    // Retornamos la respuesta
    return $this->response($code, $mensaje, $response);
}



}