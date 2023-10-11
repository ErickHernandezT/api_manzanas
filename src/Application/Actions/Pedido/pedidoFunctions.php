<?php

namespace App\Application\Actions\Pedido;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;
use Exception;
use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class pedidoFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }



    public function mostrarPedido(int $idUsuario)
    {



    }




    public function eliminarPedido(int $idPedido)
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql = "DELETE FROM pedido WHERE id = ?;";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$idPedido]);
        // Verificamos si la eliminación fue exitosa (código 200)
        return ($statement == '200') ? true : false;;
    }




    

}