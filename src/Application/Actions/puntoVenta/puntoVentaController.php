<?php

namespace App\Application\Actions\puntoVenta;

use App\Application\Actions\General\generalController;
use App\Application\Actions\puntoVenta\puntoVentaFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class puntoVentaController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new puntoVentaFunctions();
    }



    public function validarIngresarPuntoVenta($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $latitud = (isset($params['latitud'])) ? strip_tags($params['latitud']) : '';
        $longitud = (isset($params['longitud'])) ? strip_tags($params['longitud']) : '';
        $estatus = (isset($params['estatus'])) ? (int)strip_tags($params['estatus']) : 0;
        $horario = (isset($params['horario'])) ? strip_tags($params['horario']) : '';

        $uploadedFile = $request->getUploadedFiles()['foto'];

        $mensaje = ['message' => ''];

        if ($nombre != '' && $latitud != '' && $longitud != '' && $estatus > 0 && $horario != '' ) {
            // Verifica que la foto se haya cargado correctamente
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                // El archivo se cargó correctamente
                $blobData = $uploadedFile->getStream();

                $mensaje = $this->funciones->ingresarPuntoVenta($nombre, $blobData, $latitud, $longitud, $estatus, $horario);

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



    public function validarListaPuntosVenta($request, $response, $args)
    {
        

            $mensaje = $this->funciones->listaPuntosVenta();

            if ($mensaje) {
                $code = 200;
            } else {
                $code = 404;
                $mensaje = ['message' => 'Error al cargar los puntos de venta'];
            }
       

        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }




    public function validarActualizarPuntoVenta($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;
        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $latitud = (isset($params['latitud'])) ? strip_tags($params['latitud']) : '';
        $longitud = (isset($params['longitud'])) ? strip_tags($params['longitud']) : '';
        $estatus = (isset($params['estatus'])) ? (int)strip_tags($params['estatus']) : 0;
        $horario = (isset($params['horario'])) ? strip_tags($params['horario']) : '';

        $uploadedFile = $request->getUploadedFiles()['foto'];

        $mensaje = ['message' => ''];

        if ($id > 0 && $nombre != '' && $latitud != '' && $longitud != '' && $estatus > 0 && $horario != '') {
            // Verifica que la foto se haya cargado correctamente
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                // El archivo se cargó correctamente
                $blobData = $uploadedFile->getStream();

                $mensaje = $this->funciones->actualizarPuntoVenta($id, $nombre, $blobData, $latitud, $longitud, $estatus, $horario);

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
    


    public function validarEliminarPuntoVenta($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id != '') {
            

                $mensaje = $this->funciones->eliminarPuntoVenta($id);

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



    public function validarBuscarPuntoVentaPorId($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id != '') {
            

                $mensaje = $this->funciones->buscarPuntoVentaPorId($id);

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