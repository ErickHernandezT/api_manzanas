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





    public function validarMostrarCarrito($request, $response, $args)
    {

        $params = (array)$request->getParsedBody();

        $idUsuario = (isset($params['idUsuario'])) ? (int)strip_tags($params['idUsuario']) : 0;

        $mensaje = ['message' => ''];


        if ($idUsuario > 0) {
                $mensaje = $this->funciones->mostrarCarrito($idUsuario);

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




    public function validarEliminarCarrito($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $idUsuario = (isset($params['idUsuario'])) ? (int)strip_tags($params['idUsuario']) : 0;


        $mensaje = ['message' => ''];

        if ($idUsuario > 0) {
            $mensaje = $this->funciones->eliminarCarrito($idUsuario);

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






    public function validarAgregarCarrito($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $idUsuario = (isset($params['idUsuario'])) ? (int)strip_tags($params['idUsuario']) : 0;
        $idManzana = (isset($params['idManzana'])) ? (int)strip_tags($params['idManzana']) : 0;
        $cantidad = (isset($params['cantidad'])) ? (int)strip_tags($params['cantidad']) : 0;

        $mensaje = ['message' => ''];

        if ($idUsuario > 0  && $idManzana > 0  && $cantidad > 0) {
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



    public function validarModificarCarrito($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $idUsuario = (isset($params['idUsuario'])) ? (int)strip_tags($params['idUsuario']) : 0;
        $idManzana = (isset($params['idManzana'])) ? (int)strip_tags($params['idManzana']) : 0;
        $cantidad = (isset($params['cantidad'])) ? (int)strip_tags($params['cantidad']) : 0;

        $mensaje = ['message' => ''];

        if ($idUsuario > 0  && $idManzana > 0 && $cantidad > 0) {
            // Verifica que la foto se haya cargado correctamente
          

                $mensaje = $this->funciones->modificarCarrito($idUsuario, $idManzana, $cantidad);

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