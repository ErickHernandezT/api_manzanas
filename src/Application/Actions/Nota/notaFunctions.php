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



    public function ingresarNota(String $nota, int $idUsuario)
    {
        // Obtener la fecha actual en formato 'YYYY-MM-DD'
        $fechaActual = date('Y-m-d');

        // Consulta SQL para insertar la nota con la fecha actual
        $sql = "INSERT INTO nota (mensaje, fecha, idUsuario) VALUES (?, ?, ?)";

        // Ejecutar la consulta con los parámetros
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$nota, $fechaActual, $idUsuario]);

        // Verificar el resultado de la inserción
        return ($statement == '200') ? true : false;
    }



    public function eliminarNota($idNota)
    {
        $sql = "DELETE FROM nota WHERE id = ?";
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$idNota]);

        // Verificar si la eliminación fue exitosa
        return ($statement == '200') ? true : false;
    }


    public function listaNotas()
{
    $sql = "SELECT n.id, n.mensaje, n.fecha, u.usuario
            FROM nota n
            INNER JOIN usuario u ON n.idUsuario = u.id";


    $statement = $this->DB->Buscar($sql);
    
    if (is_array($statement) && count($statement) > 0) {
        return $statement;
    } else {
        return ['message' =>  "Problemas para obtener las notas"];
    }
}

}
