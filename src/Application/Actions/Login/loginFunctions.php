<?php

namespace App\Application\Actions\Login;

use App\Application\Settings\mysql;
use App\Application\Actions\General\generalController;
use PDO;

use App\functions;

class loginFunctions {

    private $DB;

    function __construct(){
        $this->DB = new mysql();
    }

    public function crearUsuario(string $nombre, string $apellidoPat, string $apellidoMat, string $correo, string $telefono, string $usuario, string $contraseniaEncriptada)
{

        // Verificar si el correo o el usuario ya existen en la base de datos
        $sqlVerificarExistencia = "SELECT COUNT(*) AS count FROM usuario WHERE correo = ? OR usuario = ?";
        $existencia = $this->DB->Buscar_Seguro_UTF8($sqlVerificarExistencia, [$correo, $usuario]);

        if (!empty($existencia) && $existencia[0]['count'] > 0) {
            return ['error' => "El correo o el usuario ya están en uso."];
        }

        $sql = "INSERT INTO usuario (nombre, apellidoPat, apellidoMat, correo, telefono, usuario, contrasenia, idRol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $save = $this->DB->Ejecutar_Seguro_UTF8($sql, [$nombre, $apellidoPat, $apellidoMat, $correo, $telefono, $usuario, $contraseniaEncriptada, 3]);
        
        if ($save == '200') {
            return ['message' => "Usuario creado con éxito"];
        } else {
            return ['error' => "No se pudo crear el usuario."];
        }
   
}



public function crearProductor(string $nombre, string $apellidoPat, string $apellidoMat, string $correo, string $telefono, string $usuario, string $contraseniaEncriptada)
{

        // Verificar si el correo o el usuario ya existen en la base de datos
        $sqlVerificarExistencia = "SELECT COUNT(*) AS count FROM usuario WHERE correo = ? OR usuario = ?";
        $existencia = $this->DB->Buscar_Seguro_UTF8($sqlVerificarExistencia, [$correo, $usuario]);

        if (!empty($existencia) && $existencia[0]['count'] > 0) {
            return ['error' => "El correo o el usuario ya están en uso."];
        }

        $sql = "INSERT INTO usuario (nombre, apellidoPat, apellidoMat, correo, telefono, usuario, contrasenia, idRol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $save = $this->DB->Ejecutar_Seguro_UTF8($sql, [$nombre, $apellidoPat, $apellidoMat, $correo, $telefono, $usuario, $contraseniaEncriptada, 2]);
        
        if ($save == '200') {
            return ['message' => "Usuario creado con éxito"];
        } else {
            return ['error' => "No se pudo crear el usuario."];
        }
   
}



}