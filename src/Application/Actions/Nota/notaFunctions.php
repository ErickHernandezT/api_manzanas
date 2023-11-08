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





    public function crearNota(int $cantidad, int $tipo, int $manzana, int $usuario)
    {
        // Obtener la fecha actual en formato 'YYYY-MM-DD'
        $fecha = date('Y-m-d');

        // Consulta SQL para insertar la nota con la fecha actual
        $sql = "INSERT INTO nota (cantidad, fecha, idTipo, idManzana, idUsuario) VALUES (?, ?, ?, ?, ?)";

        // Ejecutar la consulta con los parámetros
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$cantidad, $fecha, $tipo, $manzana, $usuario]);

        // Verificar el resultado de la inserción
        return ($statement == '200') ? true : false;
    }





    public function aceptarNota(int $nota)
{
    // Obtener información de la nota, incluyendo el idTipo
    $sql = "SELECT n.cantidad, t.nombre AS tipoNota, n.idManzana FROM nota n
            INNER JOIN tipo_Nota t ON n.idTipo = t.id
            WHERE n.id = ?";
    $notaInfo = $this->DB->Ejecutar_Seguro_UTF8($sql, [$nota]);

    // Comprobar si se encontró la nota
    if ($notaInfo) {
        $cantidad = $notaInfo['cantidad'];
        $tipoNota = $notaInfo['tipoNota'];
        $idManzana = $notaInfo['idManzana'];

        // Comprobar el tipo de nota
        if ($tipoNota == "aumentar") {
            // Incrementar el stock de la manzana
            $sqlUpdate = "UPDATE manzana SET stock = stock + ? WHERE id = ?";
            $this->DB->Ejecutar_Seguro_UTF8($sqlUpdate, [$cantidad, $idManzana]);
        } elseif ($tipoNota == "disminuir") {
            // Comprobar si la cantidad a disminuir no es mayor que el stock actual
            $sqlStock = "SELECT stock FROM manzana WHERE id = ?";
            $manzanaInfo = $this->DB->Ejecutar_Seguro_UTF8($sqlStock, [$idManzana]);

            if ($manzanaInfo && $manzanaInfo['stock'] >= $cantidad) {
                // Disminuir el stock de la manzana
                $sqlUpdate = "UPDATE manzana SET stock = stock - ? WHERE id = ?";
                $this->DB->Ejecutar_Seguro_UTF8($sqlUpdate, [$cantidad, $idManzana]);
            } else {
                return "La cantidad a disminuir es mayor que el stock actual de la manzana.";
            }
        }

        // Eliminar la nota después de procesarla
        $sqlDelete = "DELETE FROM nota WHERE id = ?";
        $statement= $this->DB->Ejecutar_Seguro_UTF8($sqlDelete, [$nota]);

        return ($statement == '200') ? true : false;
    } else {
        return "Nota no encontrada.";
    }
}



    public function rechazarNota($idNota)
    {
        $sql = "DELETE FROM nota WHERE id = ?";
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$idNota]);

        // Verificar si la eliminación fue exitosa
        return ($statement == '200') ? true : false;
    }


    public function listaNotas()
    {
        $sql = "SELECT n.id, n.cantidad, n.fecha, t.nombre AS tipoNota, m.nombre AS manzanaNota, u.usuario AS usuarioNota
            FROM nota n
            INNER JOIN tipo_Nota t ON n.idTipo = t.id
            INNER JOIN manzana m ON n.idManzana = m.id
            INNER JOIN usuario u ON n.idUsuario = u.id";


        $statement = $this->DB->Buscar($sql);

        if (is_array($statement) && count($statement) > 0) {
            return $statement;
        } else {
            return ['message' =>  "Problemas para obtener las notas"];
        }
    }



    public function listaTipoNotaSencilla()
    {
        $sql = "SELECT id, nombre FROM tipo_Nota";

        $statement = $this->DB->Buscar($sql, []);

        if (is_array($statement) && count($statement) > 0) {
            return $statement;
        } else {
            return ['message' => "Error al mostrar las manzanas"];
        }
    }



}
