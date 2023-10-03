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


    public function validarIngresarNota($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nota = (isset($params['nota'])) ? strip_tags($params['nota']) : '';
        $idUsuario = (isset($params['idUsuario'])) ? (int)strip_tags($params['idUsuario']) : 0;



        $mensaje = ['message' => ''];

        if ($nota != '' && $idUsuario > 0) {
            $mensaje = $this->funciones->ingresarNota($nota, $idUsuario);

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




    public function validarEliminarNota($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {
            $mensaje = $this->funciones->eliminarNota($id);

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



}