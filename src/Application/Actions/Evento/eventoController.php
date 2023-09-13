<?php

namespace App\Application\Actions\Evento;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Evento\eventoFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class eventoController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new eventoFunctions();
    }



    public function validarIngresarEvento($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $fechaInicio = (isset($params['fechaInicio'])) ? strip_tags($params['fechaInicio']) : '';
        $fechaFin = (isset($params['fechaFin'])) ? strip_tags($params['fechaFin']) : '';
        $latitud = (isset($params['latitud'])) ? strip_tags($params['latitud']) : '';
        $longitud = (isset($params['longitud'])) ? strip_tags($params['longitud']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';

        $uploadedFile = $request->getUploadedFiles()['foto'];

        $mensaje = ['message' => ''];

        if ($nombre != '' && $fechaInicio != '' && $fechaFin != '' && $latitud != '' &&  $longitud != '' &&  $descripcion != '' ) {
            // Verifica que la foto se haya cargado correctamente
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                // El archivo se cargó correctamente
                $blobData = $uploadedFile->getStream();

                $mensaje = $this->funciones->ingresarEvento($nombre, $blobData, $fechaInicio, $fechaFin, $latitud, $longitud, $descripcion);

                if ($mensaje) {
                    $code = 200;
                } else {
                    $code = 404;
                }
            } else {
                $code = 400;
                $mensaje = ['message' => 'Error al cargar la foto'];
            }
        } else {
            $code = 400;
            $mensaje = ['message' => 'Datos incorrectos o vacíos'];
        }

        // Retornamos la respuesta
        return $this->response($code, [$mensaje], $response);
    }



    public function validarListaEventos($request, $response, $args)
    {
        

            $mensaje = $this->funciones->listaEventos();

            if ($mensaje) {
                $code = 200;
            } else {
                $code = 404;
                $mensaje = ['message' => 'Eventos no encontrados'];
            }
       

        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }




    public function validarActualizarEvento($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;
        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $fechaInicio = (isset($params['fechaInicio'])) ? strip_tags($params['fechaInicio']) : '';
        $fechaFin = (isset($params['fechaFin'])) ? strip_tags($params['fechaFin']) : '';
        $latitud = (isset($params['latitud'])) ? strip_tags($params['latitud']) : '';
        $longitud = (isset($params['longitud'])) ? strip_tags($params['longitud']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';

        $uploadedFile = $request->getUploadedFiles()['foto'];

        $mensaje = ['message' => ''];

        if ($id > 0  && $nombre != '' && $fechaInicio != '' && $fechaFin != '' && $latitud != '' &&  $longitud != '' &&  $descripcion != '') {
            // Verifica que la foto se haya cargado correctamente
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                // El archivo se cargó correctamente
                $blobData = $uploadedFile->getStream();

                $mensaje = $this->funciones->actualizarEvento($id, $nombre, $blobData, $fechaInicio, $fechaFin, $latitud, $longitud, $descripcion );

                if ($mensaje) {
                    $code = 200;
                } else {
                    $code = 404;
                }
            } else {
                $code = 400;
                $mensaje = ['message' => 'Error al cargar la foto'];
            }
        } else {
            $code = 400;
            $mensaje = ['message' => 'Datos incorrectos o vacíos'];
        }

        // Retornamos la respuesta
        return $this->response($code, [$mensaje], $response);
    }
    


    public function validarEliminarEvento($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {
            

                $mensaje = $this->funciones->eliminarEvento($id);

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



    public function validarBuscarEventoId($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id != '') {
            

                $mensaje = $this->funciones->buscarEventoPorId($id);

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