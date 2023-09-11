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
        $sql = "INSERT INTO usuario (nombre, apellidoPat, apellidoMat, correo, telefono, usuario, contrasenia, idRol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $save = $this->DB->Ejecutar_Seguro_UTF8($sql, [$nombre, $apellidoPat, $apellidoMat, $correo, $telefono, $usuario, $contraseniaEncriptada, 2]);
        return ($save == '200') ? true : false;
    }

    public function verificarUsuario(string $usuario, string $contrasenia)
    {
        $sql = "SELECT idRol FROM usuario WHERE usuario =? AND contrasenia = ?";
        $statement = $this->DB->Buscar_Seguro($sql, [$usuario, $contrasenia]);
        if (count($statement) > 0) {
            return $statement[0];
        } 
    }



}