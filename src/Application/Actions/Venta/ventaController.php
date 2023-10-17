<?php

namespace App\Application\Actions\Venta;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Venta\ventaFunctions;
use Slim\Http\UploadedFile;
use PhpParser\Node\Expr\Cast\Double;

class ventaController extends generalController
{
    private $funciones;

    public function __construct()
    {
        $this->funciones = new ventaFunctions();
    }



    
    public function validarListaVentas($request, $response, $args)
{
    

        $mensaje = $this->funciones->listaVentas();

        if ($mensaje) {
            $code = 200;
        } else {
            $code = 404;
            $mensaje = ['message' => 'Error al cargar los pedidos'];
        }
   

    // Retornamos la respuesta
    return $this->response($code, $mensaje, $response);
}



}