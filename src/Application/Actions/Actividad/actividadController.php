<?php

namespace App\Application\Actions\Actividad;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Actividad\actividadFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class actividadController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new actividadFunctions();
    }



    public function validarIngresarActividad($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $foto = (isset($params['foto'])) ? strip_tags($params['foto']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';



        $mensaje = ['message' => ''];

        if ($nombre != '' && $descripcion != '' && $foto != '') {
            $mensaje = $this->funciones->ingresarActividad($nombre, $foto, $descripcion);

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



    public function validarListaActividades($request, $response, $args)
    {


        $mensaje = $this->funciones->listaActividades();

        if ($mensaje) {
            $code = 200;
        } else {
            $code = 404;
            $mensaje = ['message' => 'Actividades no encontradas'];
        }


        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }




    public function validarActualizarActividad($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;
        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $foto = (isset($params['foto'])) ? strip_tags($params['foto']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';



        $mensaje = ['message' => ''];

        if ($id > 0 && $nombre != '' && $foto != '' && $descripcion != '') {

            $mensaje = $this->funciones->actualizarActividad($id, $nombre, $foto, $descripcion);

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



    public function validarEliminarActividad($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {
            $mensaje = $this->funciones->eliminarActividad($id);

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



    public function validarBuscarActividadId($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {
            
            $mensaje = $this->funciones->buscarActividadPorId($id);

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
