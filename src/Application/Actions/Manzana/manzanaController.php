<?php

namespace App\Application\Actions\Manzana;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Manzana\manzanaFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class manzanaController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new manzanaFunctions();
    }

    public function validarIngresarManzanas($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();
    
        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $nivelMadurez = (isset($params['nivelMadurez'])) ? strip_tags($params['nivelMadurez']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';
        $foto = (isset($params['foto'])) ? strip_tags($params['foto']) : '';
        $estatus = (isset($params['estatus'])) ? (int)strip_tags($params['estatus']) : 0;
        $precio = (isset($params['precio'])) ? (float)strip_tags($params['precio']) : 0.0;
        $stock = (isset($params['stock'])) ? (int)strip_tags($params['stock']) : 0;
       
    
        if ($nombre != '' && $nivelMadurez != '' && $descripcion != '' && $foto!= '' && $estatus > 0 && $precio > 0 && $stock > 0 && $descripcion != '') {
            
    
            
                // Verifica que la foto se haya cargado correctamente
                $mensaje = $this->funciones->ingresarManzanas($nombre, $foto, $nivelMadurez, $descripcion, $estatus, $precio, $stock);
    
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
    


    public function validarListaManzanas($request, $response, $args)
    {
        

            $mensaje = $this->funciones->listaManzanas();

            if ($mensaje) {
                $code = 200;
            } else {
                $code = 404;
                $mensaje = ['message' => 'Error al cargar manzanas'];
            }
       

        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }




    public function validarActualizarManzanas($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();
        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;
        $nombre = (isset($params['nombre'])) ? strip_tags($params['nombre']) : '';
        $nivelMadurez = (isset($params['nivelMadurez'])) ? strip_tags($params['nivelMadurez']) : '';
        $descripcion = (isset($params['descripcion'])) ? strip_tags($params['descripcion']) : '';
        $foto = (isset($params['foto'])) ? strip_tags($params['foto']) : '';
        $estatus = (isset($params['estatus'])) ? (int)strip_tags($params['estatus']) : 0;
        $precio = (isset($params['precio'])) ? (float)strip_tags($params['precio']) : 0.0;
        $stock = (isset($params['stock'])) ? (int)strip_tags($params['stock']) : 0;
    
        if ($id > 0 && $nombre != '' && $nivelMadurez != '' && $descripcion != '' && $foto!= '' && $estatus > 0 && $precio > 0 && $stock > 0 && $descripcion != '') {
            
    
            
                // Verifica que la foto se haya cargado correctamente
                $mensaje = $this->funciones->actualizarManzanas($id, $nombre, $foto, $nivelMadurez, $descripcion, $estatus, $precio, $stock);
    
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
    


    public function validarEliminarManzana($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id != '') {
            

                $mensaje = $this->funciones->eliminarManzana($id);

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



    public function validarBuscarManzanaPorId($request, $response, $args)
    {
        $params = (array)$request->getParsedBody();

        $id = (isset($params['id'])) ? (int)strip_tags($params['id']) : 0;


        $mensaje = ['message' => ''];

        if ($id > 0) {
            

                $mensaje = $this->funciones->buscarManzanaPorId($id);

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
