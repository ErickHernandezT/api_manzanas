<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Login\loginController;
use App\Application\Actions\Manzana\manzanaController;
use App\Application\Actions\puntoVenta\puntoVentaController;

return function (App $app) {
    $app->add(new Tuupola\Middleware\JwtAuthentication([
        // Rutas que requieren el token
        "path" => [
            "/Manzana"
        ],
        // "Rutas que no requieren el token"
        "secret" => 'a84125e55c207450dba07c6cb3e7b999',
        "before" => function ($request, $arguments) use ($app) {
            $token = $arguments["decoded"];
            return $request;
        },
        "error" => function ($response, $args) {
            $data["statusCode"] = 401;
            $data["message"] = "Token no valido";
            $response->getBody()->write(
                json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
            return $response->withHeader("Content-Type", "application/json");
        }
    ]));


    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    //get para mostrar la pagina de login y post para hacer la petición a la BD
    $app->get('/login', loginController::class . ':login');
    $app->post('/login', loginController::class . ':validarLogin');
    $app->post('/registrarUsuario', loginController::class . ':validarCrearUsario');



    // Grupo para manzanas
    $app->group('/Manzana', function (Group $group) {
        //ruta para ingresar una manzana
        $group->post('/registrar', manzanaController::class . ':validarIngresarManzanas');
        $group->post('/lista', manzanaController::class . ':validarListaManzanas');
        $group->post('/buscar', manzanaController::class . ':validarBuscarManzanaPorId');
        $group->post('/actualizar', manzanaController::class . ':validarActualizarManzanas');
        $group->post('/eliminar', manzanaController::class . ':validarEliminarManzana');
    });


    //Grupo para puntos de venta
    $app->group('/puntoVenta', function (Group $group) {
        //ruta para ingresar una manzana
        $group->post('/registrar', puntoVentaController::class . ':validarIngresarPuntoVenta');
        $group->post('/lista', puntoVentaController::class . ':validarListaPuntosVenta');
        $group->post('/buscar', puntoVentaController::class . ':validarBuscarPuntoVentaPorId');
        $group->post('/actualizar', puntoVentaController::class . ':validarActualizarPuntoVenta');
        $group->post('/eliminar', puntoVentaController::class . ':validarEliminarPuntoVenta');
    });



};
