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



    

}