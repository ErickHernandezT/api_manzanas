<?php

namespace App\Application\Actions\derivadosManzana;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;

class derivadosManzanaFunctions
{

    private $DB;

    function __construct()
    {
        $this->DB = new mysql();
    }

    public function listaDerivadosManzana()
    {
        // Selecciona la columna 'foto' en la consulta SQL
        $sql = "SELECT id, nombre, foto, descripcion FROM derivados_Manzana";
    
        $statement = $this->DB->Buscar($sql, []);
    
        if (is_array($statement) && count($statement) > 0) {
            // Codifica la imagen en formato base64 y agrégala al resultado
            foreach ($statement as &$row) {
                if ($row['foto'] !== null) {
                    $row['foto'] = base64_encode($row['foto']);
                }
            }
            return $statement;
        } else {
            return ['message' =>  $statement];
        }
    }



    public function buscarDerivadoManzanaPorId(int $id)
    {
        // Query SQL para buscar una manzana por su ID
        $sql = "SELECT id, nombre, foto, descripcion FROM derivados_Manzana WHERE id = ?";
    
        // Ejecutamos la consulta
        $result = $this->DB->Buscar_Seguro_UTF8($sql, [$id]);
    
        if (is_array($result) && count($result) > 0) {
            // Codifica la imagen en formato base64 si la foto no es nula
            if ($result[0]['foto'] !== null) {
                $result[0]['foto'] = base64_encode($result[0]['foto']);
            }
            return $result[0]; // Devuelve la primera fila que coincide con el ID
        } else {
            return ['message' => 'No se encontró ninguna producto derivado con el ID proporcionado'];
        }
    }



    public function ingresarDerivadoManzana(String $nombre, $blobData, String $descripcion)
    {
        // Se usa left join para que también muestre los productos que no tengan
        $sql2 = "INSERT INTO derivados_Manzana (nombre, foto, descripcion) VALUES (?, ?, ?)";

        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql2, [$nombre, $blobData, $descripcion]);
        return ($statement == '200') ? true : false;
    }



    public function actualizarDerivadoManzana(int $id, String $nombre, $blobData, String $descripcion)
    {
        // Query SQL para actualizar los datos en la tabla manzana
        $sql = "UPDATE derivados_Manzana SET nombre = ?, foto = ?, descripcion = ? WHERE id = ?";

        // Agregamos el ID como último valor en el array de parámetros
        $parametros = [$nombre, $blobData, $descripcion, $id];

        // Ejecutamos la consulta
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, $parametros);

        // Verificamos si la actualización fue exitosa (código 200)
        return ($statement == '200') ? true : false;
    }



    public function eliminarDerivadoManzana(int $id)
    {
        // Query SQL para eliminar una entrada de la tabla manzana por ID
        $sql = "DELETE FROM derivados_Manzana WHERE id = ?";

        // Ejecutamos la consulta
        $statement = $this->DB->Ejecutar_Seguro_UTF8($sql, [$id]);

        // Verificamos si la eliminación fue exitosa (código 200)
        return ($statement == '200') ? true : false;;
    }

}