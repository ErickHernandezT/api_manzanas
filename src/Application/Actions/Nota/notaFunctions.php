<?php

namespace App\Application\Actions\Nota;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class notaFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }



    public function ingresarNota(String $mensaje, int $idUsuario)
    {
        
        $sql = "INSERT INTO nota (mensaje, fecha, idUsuario) VALUES (?, ?, ?)";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$mensaje, $idUsuario]);
        return ($statement == '200') ? true : false;
    }


}