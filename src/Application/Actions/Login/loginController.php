<?php

namespace App\Application\Actions\Login;

use App\Application\Actions\General\generalController;
use App\Application\Actions\Login\loginFunctions;
use Firebase\JWT\JWT;


class loginController extends generalController
{

    private $funciones;
    public function __construct()
    {
        $this->funciones = new loginFunctions();
    }

    //Mostrar la pagina de login
    public function login($request, $response, $args)
    {

        $response->getBody()->write('Aqui va la página de login ');
        return $response;
    }

    //Validar los datos que mandan del login
    public function validarLogin($request, $response, $args)
    {
        // Mensaje en caso de que no esté bien el usuario o contraseña
        $mensaje = ['message' => ''];

        // Se validan el usuario y contraseña por protección y se tipean.
        $params = (array)$request->getParsedBody();
        $usuario = isset($params['usuario']) ? strip_tags($params['usuario']) : '';
        $contrasenia = isset($params['contrasenia']) ? strip_tags($params['contrasenia']) : '';
        

        // Validar el usuario y contraseña desde las funciones
        if ($usuario != '' && $contrasenia != '') {
            $code = 200;

            // Encriptar la contraseña ingresada para compararla con la almacenada en la base de datos
            $contraseniaEncriptada = md5($contrasenia);
            $mensaje = $this->funciones->verificarUsuario($usuario, $contraseniaEncriptada);

            if ($mensaje) {
                // En este caso, existe el usuario por lo que se genera el token
                // FALTA VALIDAR SI TE HA DADO UN USUARIO VÁLIDO (HECHO)
                $key = 'a84125e55c207450dba07c6cb3e7b999';
                $payload = [
                    'usuario' => $usuario,
                    'exp' => time() + 1800 //Media hora valida para el token antes de que expire
                    // Puedes agregar más datos al payload si lo deseas
                ];
                $token = JWT::encode($payload, $key);

                // Agrega el token al mensaje de respuesta
                $mensaje['token'] = $token;
            }else{
                $mensaje = ['message' => 'Usuario o contraseña incorrecta'];
            }
        } else {
            $code = 400;
            $mensaje = ['message' => 'Ingrese un usuario o contraseña valida'];
        }

        // Retornamos la respuesta
        return $this->response($code, $mensaje, $response);
    }




    //Mostar página de registro de usuario
    public function crearUsario($request, $response, $args)
    {

        $response->getBody()->write('Aqui va la página de creación de un nuevo usuario ');
        return $response;
    }


    //Se validan los datos para la creación de un usuario
    public function validarCrearUsario($request, $response, $args)
    {

        //se validan los datos para crear un usuario.
        $params    = (array)$request->getParsedBody();
        $nombre   = (isset($params['nombre']))   ? strip_tags($params['nombre'])   : '';
        $apellidoPat   = (isset($params['apellidoPat']))   ? strip_tags($params['apellidoPat'])   : '';
        $apellidoMat  = (isset($params['apellidoMat']))   ? strip_tags($params['apellidoMat'])   : '';
        $correo   = (isset($params['correo']))   ? strip_tags($params['correo'])   : '';
        $telefono   = (isset($params['telefono']))   ? strip_tags($params['telefono'])   : '';
        $usuario   = (isset($params['usuario']))   ? strip_tags($params['usuario'])   : '';
        $contrasenia  = (isset($params['contrasenia']))   ? strip_tags($params['contrasenia'])   : '';

        $contraseniaEncriptada = md5($contrasenia);

        if ($telefono != '' && $correo != '' && $apellidoMat != '' && $apellidoPat != '' && $nombre != '' && $usuario != '' && $correo != '' && $contraseniaEncriptada != '') {

            $respuesta = $this->funciones->crearUsuario($nombre, $apellidoPat, $apellidoMat, $correo, $telefono, $usuario, $contraseniaEncriptada);

            if ($respuesta) {
                $code = 200;
            } else {
                $code = 400;
            }
        } else {
            $respuesta =  ['message' => 'Valores vacíos'];
            $code = 400;
        }


        return $this->response($code, [$respuesta], $response);
    }
}
