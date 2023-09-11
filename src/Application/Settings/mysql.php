<?php
//En esta clase hacemos todas las operaciones de la Base de Datos
//Agregamos el archivo config que tienen las variables de conexion
namespace App\Application\Settings;
//En esta clase hacemos todas las operaciones de la Base de Datos
//Agregamos el archivo config que tienen las variables de conexion
use App\Application\Settings\config;
use \PDO;
use \PDOException;

class mysql
{
    //Creamos la variable de la conexion
    private $conn;

    //Variables para guardar errores en txt
    private $file = 'errores.txt';

    //Creamos la variable del Ultimo elemento Guardado
    private $ultimoId = null;


    protected $_HOST = '';
    protected $_DATABASE = '';
    protected $_USER = '';
    protected $_PASSWORD = '';

    public function __construct(string $database = '', string $user = '', string $password = '', string $host = '')
    {
        if ($database == '' || $user == '' || $password == '') {
            $this->_HOST = config::SERVERNAME;
            $this->_DATABASE = config::DATABASE;
            $this->_USER = config::USERNAME;
            $this->_PASSWORD = config::PASSWORD;
        } else {
            $this->_DATABASE = $database;
            $this->_USER = $user;
            $this->_PASSWORD = $password;
        }
    }

    /**
     * Esta funcion establece la conexion a la BD
     * @return boolean Regresa true si la conexion fue exitosa o false si no se pudo establecer conexion
     */
    private function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->_HOST . ";dbname=" . $this->_DATABASE, $this->_USER, $this->_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            // $this->exepciones("Error de conexion: " . $e->getMessage());
            return "Error de conexion: " . $e->getMessage();
            // return false;
        }
    }

    /**
     * Esta funcion establece la conexion a la BD pero con codificación UTF8
     * @return boolean Regresa true si la conexion fue exitosa o false si no se pudo establecer conexion
     */
    private function connect_UTF8()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->_HOST . ";dbname=" . $this->_DATABASE, $this->_USER, $this->_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            // $this->exepciones("Error de conexion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Esta funcion cierra la conexion
     */
    private function close()
    {
        $this->conn = null;
    }

    /**
     * Esta funcion guarda los errores que se puedan generar en un archivo llamado errores.txt
     */
    private function exepciones($msg)
    {
        $fileOpen = fopen($this->file, "r+");
        fwrite($fileOpen, $msg);
        fclose($fileOpen);
        return;
    }

    /**
     * Esta funcion sirve para consultar información a la base de datos sin pasar parametros
     * @param string $sql Recibe la consulta sql tipo SELECT que sera ejecutada
     * @return array regresa un arreglo con los resultados de la consulta
     */
    public function Buscar($sql)
{
    try {
        if ($this->connect()) { //se establece la conexión
            $stmt = $this->conn->prepare($sql); //preparamos la consulta
            $stmt->execute(); //se ejecuta
            $stmt->setFetchMode(PDO::FETCH_ASSOC); //la convertimos a un arreglo
            $res = $stmt->fetchAll(); //Guardamos el arreglo en una variable

            // Recorre el arreglo y verifica la codificación
            array_walk_recursive($res, function (&$item, $key) {
                if (is_string($item)) {
                    $currentEncoding = mb_detect_encoding($item, 'UTF-8, ISO-8859-1', true);
                    if ($currentEncoding !== 'UTF-8') {
                        $item = mb_convert_encoding($item, 'UTF-8', $currentEncoding);
                    }
                }
            });

            $this->conn = null;
            return $res; //regresamos el resultado
        } else {
            return array();
        }
    } catch (PDOException $e) {
        return "500";
    }
}


    /**
     * Esta funcion ejecuta un SELECT que pasa parametros 
     * Usa sentencias preparadas
     * @param string $sql Es la consulta tipo SELECT que se va a ejecutar remplazando los parametros usados por signos de interrogacion (?)
     * @param array $bind Es un arreglo con los parametros que se usaran en la consulta ordenados 
     * @return array regresa un arreglo con los resultados de la consulta
     */
    public function Buscar_Seguro($sql, $bind)
    {
        try {
            if (count($bind) > 0 && $this->connect()) {
                $stmt = $this->conn->prepare($sql);
                $c = 1;
                for ($i = 0; $i < count($bind); $i++) {

                    $validation = FALSE;

                    if (is_int($bind[$i])) {
                        $validation = PDO::PARAM_INT;
                    } else if (is_bool($bind[$i])) {
                        $validation = PDO::PARAM_BOOL;
                    } else if (is_null($bind[$i])) {
                        $validation = PDO::PARAM_NULL;
                    } else {
                        $validation = PDO::PARAM_STR;
                    }

                    if (is_null($bind[$i]) || $bind[$i] == "NULL")
                        $validation = \PDO::PARAM_NULL;

                    $stmt->bindValue($c, $bind[$i], $validation);
                    $c++;
                }
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                array_walk_recursive($res, function (&$item, $key) {
                    if (is_string( $item )) {
                        if (mb_detect_encoding($item, 'utf-8', true) === false) {
                            $item = utf8_encode($item);
                        }
                    }
                });
                $this->conn = null;
                return $res;
            } else {
                return "500";
            }
        } catch (PDOException $e) {
            // return "500";
            $texto = $sql . "<br>" . $e->getMessage();
            return $texto;
            // $this->exepciones("Error de Buscar_Seguro: " . $texto);
            // $error = "Ocurrio un error al intentar ejecutar la consulta - B2";
            // return $error;
        }
    }

    /**
     * Esta funcion sirve para consultar información a la base de datos sin pasar parametros usando la codificacion UTF en la conexion
     * @param string $sql Recibe la consulta sql tipo SELECT que sera ejecutada
     * @return array regresa un arreglo con los resultados de la consulta
     */
    public function Buscar_UTF8($sql)
    {
        try {
            if ($this->connect_UTF8()) { //se establece la conexión
                $stmt = $this->conn->prepare($sql); //preparamos la consulta
                $stmt->execute(); //se ejecuta
                $stmt->setFetchMode(PDO::FETCH_ASSOC); //la convertimos a una arreglo
                $res = $stmt->fetchAll(); //Guardamos el arreglo en una variable
                $this->conn = null;
                return $res; //regresamos el resultado
            } else {
                return array();
            }
        } catch (PDOEXception $e) {
            return "500";
            // $this->exepciones("Error de Buscar_UTF8: " . $sql . "\n" . $e->getMessage());
            // $error = "Ocurrio un error al intentar ejecutar la consulta - BC1";
            // return $error;
        }
    }

    /**
     * Esta funcion ejecuta un SELECT que pasa parametros 
     * Usa sentencias preparadas -- Esta usa la codificacion UTF8 en la conexion
     * @param string $sql Es la consulta tipo SELECT que se va a ejecutar remplazando los parametros usados por signos de interrogacion (?)
     * @param array $bind Es un arreglo con los parametros que se usaran en la consulta ordenados 
     * @return array regresa un arreglo con los resultados de la consulta
     */
    public function Buscar_Seguro_UTF8($sql, $bind)
    {
        try {
            if (count($bind) > 0 && $this->connect_UTF8()) {
                $stmt = $this->conn->prepare($sql);
                $c = 1;
                for ($i = 0; $i < count($bind); $i++) {

                    $validation = FALSE;

                    if (is_int($bind[$i])) {
                        $validation = PDO::PARAM_INT;
                    } else if (is_bool($bind[$i])) {
                        $validation = PDO::PARAM_BOOL;
                    } else if (is_null($bind[$i])) {
                        $validation = PDO::PARAM_NULL;
                    } else {
                        $validation = PDO::PARAM_STR;
                    }

                    if (is_null($bind[$i]) || $bind[$i] == "NULL")
                        $validation = \PDO::PARAM_NULL;

                    $stmt->bindValue($c, $bind[$i], $validation);
                    $c++;
                }
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->conn = null;
                return $res;
            } else {
                return "500";
            }
        } catch (PDOException $e) {
            // return "500";
            $texto = $sql . "<br>" . $e->getMessage();
            return $texto;
            // $this->exepciones("Error de Buscar_Seguro_UTF8: " . $texto);
            // $error = "Ocurrio un error al intentar ejecutar la consulta - BC2";
            // return $error;
        }
    }

    /**
     * Esta funcion sirve para ejecutar las operaciones INSERT, UPDATE o DELETE y que no tienen parametros
     * @param string $sql Es la consulta a ejecutar de tipo INSERT, UPDATE O DELETE
     * @return string|boolean Regresa un string con un codigo 200 en caso de exito, FALSE en caso de problemas de conexion o un texto generico en caso de que la consulta presente problemas de ejecucion
     */
    public function Ejecutar($sql)
    {
        try {
            if ($this->connect()) {
                $res = $this->conn->exec($sql);
                $this->ultimoId = $this->conn->lastInsertId();
                $this->conn = null;
                return "200";
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "500";
            // $this->exepciones("Error de Ejecutar: " . $sql . "\n" . $e->getMessage());
            // $error = "Ocurrio un error al intentar ejecutar la consulta - E1";
            // return $error;
        }
    }

    /**
     * Esta funcion sirve para ejecutar las operaciones INSERT, UPDATE o DELETE y que no tienen parametros y llevan caracteres que necesitan ser codificados en UTF8
     * @param string $sql Es la consulta a ejecutar de tipo INSERT, UPDATE O DELETE
     * @return string|boolean Regresa un string con un codigo 200 en caso de exito, FALSE en caso de problemas de conexion o un texto generico en caso de que la consulta presente problemas de ejecucion
     */
    public function Ejecutar_UTF8($sql)
    {
        try {
            if ($this->connect_UTF8()) {
                $res = $this->conn->exec($sql);
                $this->ultimoId = $this->conn->lastInsertId();
                $this->conn = null;
                return "200";
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "500";
            // $this->exepciones("Error de Ejecutar_UTF8: " . $sql . "\n" . $e->getMessage());
            // $error = "Ocurrio un error al intentar ejecutar la consulta - EC1";
            // return $error;
        }
    }

    /**
     * Esta funcion sirve para ejecutar las operaciones INSERT, UPDATE o DELETE y pasan parametros 
     * @param string $sql Es la consulta sql que se ejecutara con los parametros reemplazados por signos de interrogacion (?)
     * @param array Son los parametros que seran reemplazados en la consulta
     * @return string Regresa un string con un codigo 200 en caso de exito, 500 en caso de problemas de conexion o un texto generico en caso de que la consulta presente problemas de ejecucion
     */
    public function Ejecutar_Seguro($sql, $bind)
    {
        try {
            if (count($bind) > 0 && $this->connect()) {
                $stmt = $this->conn->prepare($sql);
                $c = 1;
                for ($i = 0; $i < count($bind); $i++) {

                    $validation = FALSE;

                    if (is_int($bind[$i])) {
                        $validation = PDO::PARAM_INT;
                    } else if (is_bool($bind[$i])) {
                        $validation = PDO::PARAM_BOOL;
                    } else if (is_null($bind[$i])) {
                        $validation = PDO::PARAM_NULL;
                    } else {
                        $validation = PDO::PARAM_STR;
                    }

                    if (is_null($bind[$i]) || $bind[$i] == "NULL")
                        $validation = \PDO::PARAM_NULL;
                    $stmt->bindValue($c, $bind[$i], $validation);
                    $c++;
                }
                $stmt->execute();
                $this->ultimoId = $this->conn->lastInsertId();
                $this->conn = null;
                return "200";
            } else {
                return "500";
            }
        } catch (PDOException $e) {
            return "501";
            // $texto = $sql . "<br>" . $e->getMessage();
            // return $texto;
            // $this->exepciones("Error de Ejecutar_Seguro: " . $texto);
            // $error = "Ocurrio un error al intentar ejecutar la consulta - E2";
            // return $error;
        }
    }

    /**
     * Esta funcion sirve para ejecutar las operaciones INSERT, UPDATE o DELETE y pasan parametros que se necesitan codifocar con utf8
     * @param string $sql Es la consulta sql que se ejecutara con los parametros reemplazados por signos de interrogacion (?)
     * @param array Son los parametros que seran reemplazados en la consulta
     * @return string Regresa un string con un codigo 200 en caso de exito, 500 en caso de problemas de conexion o un texto generico en caso de que la consulta presente problemas de ejecucion
     */
    public function Ejecutar_Seguro_UTF8($sql, $bind)
    {
        try {
            if (count($bind) > 0 && $this->connect_UTF8()) {
                $stmt = $this->conn->prepare($sql);
                $c = 1;
                for ($i = 0; $i < count($bind); $i++) {

                    $validation = FALSE;

                    if (is_int($bind[$i])) {
                        $validation = PDO::PARAM_INT;
                    } else if (is_bool($bind[$i])) {
                        $validation = PDO::PARAM_BOOL;
                    } else if (is_null($bind[$i])) {
                        $validation = PDO::PARAM_NULL;
                    } else {
                        $validation = PDO::PARAM_STR;
                    }

                    if (is_null($bind[$i]) || $bind[$i] == "NULL")
                        $validation = \PDO::PARAM_NULL;
                    $stmt->bindValue($c, $bind[$i], $validation);
                    $c++;
                }
                $stmt->execute();
                $this->ultimoId = $this->conn->lastInsertId();
                $this->conn = null;
                return "200";
            } else {
                return "500";
            }
        } catch (PDOException $e) {
            // return "501";
            $texto = $e->getMessage();
            return $texto;
            // $this->exepciones("Error de Ejecutar_Seguro_UTF8: " . $texto);
            // $error = "Ocurrio un error al intentar ejecutar la consulta - EC2";
            // return $error;
        }
    }

    public function getUltimoIdInsertado()
	{
		return $this->ultimoId;
	}
}
