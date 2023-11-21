<?php

namespace App\Application\Actions\Actividad;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class actividadFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }



    public function ingresarActividad(String $nombre, String $foto, String $descripcion)
    {
        // Se usa left join para que también muestre los productos que no tengan
        $sql2 = "INSERT INTO actividad (nombre, foto, descripcion) VALUES (?, ?, ?)";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql2, [$nombre, $foto, $descripcion]);
        return ($statement == '200') ? true : false;
    }



    public function actualizarActividad(int $id, String $nombre, String $foto, String $descripcion)
    {
        // Query SQL para actualizar los datos en la tabla actividad
        $sql = "UPDATE actividad SET nombre = ?, foto = ?, descripcion = ? WHERE id = ?";

        // Agregamos el ID como último valor en el array de parámetros
        $parametros = [$nombre, $foto, $descripcion, $id];

        // Ejecutamos la consulta
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, $parametros);

        // Verificamos si la actualización fue exitosa (código 200)
        return ($statement == '200') ? true : false;
    }



    public function eliminarActividad(int $id)
    {
        // Query SQL para eliminar una entrada de la tabla actividad por ID
        $sql = "DELETE FROM actividad WHERE id = ?";

        // Ejecutamos la consulta
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$id]);

        // Verificamos si la eliminación fue exitosa (código 200)
        return ($statement == '200') ? true : false;
    }

    

    public function listaActividades()
    {
        // consulta sql
        $sql = "SELECT id, nombre, foto, descripcion FROM actividad";
    
        $statement = $this->DB->Buscar($sql, []);
    
        if (is_array($statement) && count($statement) > 0) {
            return $statement;
        } else {
            return ['message' =>  "No se pudieron obtener la lista de actividades"];
        }
    }



    public function buscarActividadPorId(int $id)
    {
        // Query SQL para buscar una actividad por su ID
        $sql = "SELECT id, nombre, foto, descripcion FROM actividad WHERE id = ?";
    
        // Ejecutamos la consulta
        $statement = $this->DB->Buscar_Seguro_UTF8($sql, [$id]);
    
        if (is_array($statement) && count($statement) > 0) {
            return $statement[0]; // Devuelve la primera fila que coincide con el ID
        } else {
            return ['message' => 'No se encontró ninguna actividad con el ID proporcionado'];
        }
    }



    

}