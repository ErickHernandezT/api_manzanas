<?php

namespace App\Application\Actions\Carrito;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Carrito\carritoFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class carritoController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new carritoFunctions();
    }




    public function validarAgregarCarrito($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $idUsuario = (isset($params['idUsuario'])) ? strip_tags($params['idUsuario']) : '';
        $idManzana = (isset($params['idManzana'])) ? strip_tags($params['idManzana']) : '';
        $cantidad = (isset($params['cantidad'])) ? (int)strip_tags($params['cantidad']) : 0;

        $mensaje = ['message' => ''];

        if ($idUsuario != '' && $idManzana != '' && $cantidad > 0) {
            // Verifica que la foto se haya cargado correctamente
          

                $mensaje = $this->funciones->agregarAlCarrito($idUsuario, $idManzana, $cantidad);

                if ($mensaje) {
                    $code = 200;
                } else {
                    $code = 404;
                }

        } else {
            $code = 400;
            $mensaje = ['message' => 'Datos incorrectos o vacíos'];
        }

        // Retornamos la respuesta
        return $this->response($code, [$mensaje], $response);
    }

}