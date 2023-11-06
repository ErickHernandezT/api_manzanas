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
        $precioKilo = (isset($params['precioKilo'])) ? (float)strip_tags($params['precioKilo']) : 0.0;
        $precioCaja = (isset($params['precioCaja'])) ? (float)strip_tags($params['precioCaja']) : 0.0;
        $precioTonelada = (isset($params['precioTonelada'])) ? (float)strip_tags($params['precioTonelada']) : 0.0;
        $stock = (isset($params['stock'])) ? (int)strip_tags($params['stock']) : 0;
        $categoria = (isset($params['categoria'])) ? (int)strip_tags($params['categoria']) : 0;
       
    
        if ($nombre != '' && $nivelMadurez != '' && $descripcion != '' && $foto!= '' && $estatus > 0 && $precioKilo > 0 
        && $precioCaja > 0 && $precioTonelada > 0 && $stock > 0 && $categoria != '') {
            
    
            
                // Verifica que la foto se haya cargado correctamente
                $mensaje = $this->funciones->ingresarManzana($nombre, $foto, $nivelMadurez, $descripcion, $estatus, 
                $precioKilo, $precioCaja, $precioTonelada, $stock, $categoria);
    
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
        $precioKilo = (isset($params['precioKilo'])) ? (float)strip_tags($params['precioKilo']) : 0.0;
        $precioCaja = (isset($params['precioCaja'])) ? (float)strip_tags($params['precioCaja']) : 0.0;
        $precioTonelada = (isset($params['precioTonelada'])) ? (float)strip_tags($params['precioTonelada']) : 0.0;
        $stock = (isset($params['stock'])) ? (int)strip_tags($params['stock']) : 0;
        $categoria = (isset($params['categoria'])) ? (int)strip_tags($params['categoria']) : 0;
    
        if ($id > 0 && $nombre != '' && $nivelMadurez != '' && $descripcion != '' && $foto!= '' && $estatus > 0 && $precioKilo > 0 &&
        $precioCaja > 0 && $precioTonelada > 0 && $stock > 0 && $categoria != '') {
            
    
            
                // Verifica que la foto se haya cargado correctamente
                $mensaje = $this->funciones->actualizarManzana($id, $nombre, $foto, $nivelMadurez, $descripcion, $estatus, 
                $precioKilo, $precioCaja, $precioTonelada, $stock, $categoria);
    
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
