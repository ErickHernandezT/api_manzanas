<?php

namespace App\Application\Actions\Nota;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Nota\notaFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class notaController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new notaFunctions();
    }


    public function validarCrearNota($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $cantidad = (isset($params['cantidad'])) ? (int)strip_tags($params['cantidad']) : 0;
        $tipo = (isset($params['tipo'])) ? (int)strip_tags($params['tipo']) : 0;
        $manzana = (isset($params['manzana'])) ? (int)strip_tags($params['manzana']) : 0;
        $usuario = (isset($params['usuario'])) ? (int)strip_tags($params['usuario']) : 0;



        $mensaje = ['message' => ''];

        if ($cantidad > 0 && $tipo > 0 && $manzana > 0 && $usuario > 0) {
            $mensaje = $this->funciones->crearNota($cantidad, $tipo, $manzana, $usuario);

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


    public function validarAceptarNota($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nota = (isset($params['nota'])) ? (int)strip_tags($params['nota']) : 0;



        $mensaje = ['message' => ''];

        if ($nota > 0) {
            $mensaje = $this->funciones->aceptarNota($nota);

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



    public function validarRechazarNota($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nota = (isset($params['nota'])) ? (int)strip_tags($params['nota']) : 0;


        $mensaje = ['message' => ''];

        if ($nota > 0) {
            $mensaje = $this->funciones->rechazarNota($nota);

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



    public function validarListaNotas($request, $response, $args)
    {

        $mensaje = $this->funciones->listaNotas();

        if ($mensaje) {
            $code = 200;
        } else {
            $code = 404;
            $mensaje = ['message' => 'Actividades no encontradas'];
        }

        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }



    public function validarListaTipoNotaSencilla($request, $response, $args)
    {


        $mensaje = $this->funciones->listaTipoNotaSencilla();

        if ($mensaje) {
            $code = 200;
        } else {
            $code = 404;
            $mensaje = ['message' => 'Actividades no encontradas'];
        }


        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }
}
