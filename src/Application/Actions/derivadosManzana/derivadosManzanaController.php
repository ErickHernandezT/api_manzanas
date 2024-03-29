<?php

namespace App\Application\Actions\derivadosManzana;

use App\Application\Actions\General\generalController;
use App\Application\Actions\derivadosManzana\derivadosManzanaFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class derivadosManzanaController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new derivadosManzanaFunctions();
    }



    public function validarIngresarDerivadoManzana($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $foto = (isset($params['foto'])) ? strip_tags($params['foto']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';



        if ($nombre != '' && $foto != '' && $descripcion != '') {

            $mensaje = $this->funciones->ingresarDerivadoManzana($nombre, $foto, $descripcion);

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



    public function validarListaDerivadosManzana($request, $response, $args)
    {
        $mensaje = $this->funciones->listaDerivadosManzana();

        if ($mensaje) {
            $code = 200;
        } else {
            $code = 404;
            $mensaje = ['message' => 'Productos derivados de manzana no encontrados'];
        }

        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }




    public function validarActualizarDeribadoManzana($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;
        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $foto = (isset($params['foto'])) ? strip_tags($params['foto']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';

        if ($id > 0 && $nombre != '' && $foto != '' && $descripcion != '') {

            $mensaje = $this->funciones->actualizarDerivadoManzana($id, $nombre, $foto, $descripcion);

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



    public function validarEliminarDerivadoManzana($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {


            $mensaje = $this->funciones->eliminarDerivadoManzana($id);

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



    public function validarBuscarDerivadoManzanaPorId($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {

            $mensaje = $this->funciones->buscarDerivadoManzanaPorId($id);

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
