<?php

declare(strict_types=1);

use App\Application\Actions\Actividad\actividadController;
use App\Application\Actions\Carrito\carritoController;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Login\loginController;
use App\Application\Actions\Manzana\manzanaController;
use App\Application\Actions\puntoVenta\puntoVentaController;

function CargarImagenBase64($directorio_destino, $nombre, $tmp_name)
{
    // Imagen base64 enviada desde javascript en el formulario
    // En este caso, con PHP plano podriamos obtenerla usando :
    // $baseFromJavascript = $_POST['base64'];
    $baseFromJavascript = $tmp_name;

    // Nuestro base64 contiene un esquema Data URI (data:image/png;base64,)
    // que necesitamos remover para poder guardar nuestra imagen
    // Usa explode para dividir la cadena de texto en la , (coma)
    $base_to_php = explode(',', $baseFromJavascript);
    // El segundo item del array base_to_php contiene la información que necesitamos (base64 plano)
    // y usar base64_decode para obtener la información binaria de la imagen
    $data = base64_decode($base_to_php[1]);
   
    $extencion = "jpeg";

    // Proporciona una locación a la nueva imagen (con el nombre y formato especifico)
    $nombre_final = $directorio_destino . "/" . $nombre . "." . $extencion;
    $nombre_corto = $nombre . "." . $extencion;
    $filepath = $nombre_final; // or image.jpg

    // Finalmente guarda la imágen en el directorio especificado y con la informacion dada
    file_put_contents($filepath, $data);
    return (array("ruta" => $nombre_final, "nombre" => $nombre_corto));
}

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
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });


    //ruta para loguearse
    $app->post('/login', loginController::class . ':validarLogin');
    //ruta para registrarun usuario
    $app->post('/registrarUsuario', loginController::class . ':validarCrearUsario');
    //ruta para registrarun productor
    $app->post('/registrarProductor', loginController::class . ':validarCrearProductor');



    $app->get('/image/{nombre}', function (Request $request, Response $response, $data) {
        $imagen = ( isset( $data['nombre'] ) ) ? strip_tags( $data['nombre'] ) : '';
        // $response->getBody()->write('Holaaaaaa');
        // return $response;
        if ($imagen != '') {
            $file ='../src/images/' .$imagen . '.jpeg';
            if (!file_exists($file)) {
                die("file:$file");
            }
            $image = file_get_contents($file);
            if ($image === false) {
                die("error getting image");
            }
            $response->getBody()->write($image);
            return $response->withHeader('Content-Type', 'image/jpeg');
        } else {
            die("error getting image");
        }
    }); 




    $app->post('/saveImage/{data}', function (Request $request, Response $response, $args) {
        
        $params = (array)$request->getParsedBody();
        $foto = ( isset( $params['foto'] ) ) ? strip_tags( $params['foto'] ) : '';

        if($foto != ''){
            $nombre = 'Upload-' . uniqid() . '-' . date('dmY');
            $imageFolder = "../src/images/";
            $return = "https://nuconnect.mx/api/image/";
            $valores = CargarImagenBase64($imageFolder, $nombre, $foto);
            $data = array(
                'fileName' => $valores,
                'uploaded' => 1,
                'url' => $foto
            );
            $response->getBody()->write(json_encode($data));
            return $response;
        }else{
            $mensaje = ['message' => 'Datos incorrectos o vacíos'];
            
            return $mensaje;
        }
            
        // return response( 200, $data, $response );
    });


    //Grupo para Actividades
    $app->group('/actividad', function (Group $group) {
        //ruta para ingresar una actividad
        $group->post('/registrar', actividadController::class . ':validarIngresarActividad');
        //ruta para listar las actividades
        $group->post('/lista', actividadController::class . ':validarListaActividades');
        //ruta para buscar una actividad por id
        $group->post('/buscar', actividadController::class . ':validarBuscarActividadId');
        //ruta para actualizar una actividad
        $group->post('/actualizar', actividadController::class . ':validarActualizarActividad');
        //ruta para eliminar actividad
        $group->post('/eliminar', actividadController::class . ':validarEliminarActividad');
    });



    //Grupo para Carrito
    $app->group('/carrito', function (Group $group) {
        //ruta para agregar productos al carrito
        $group->post('/agregarCarrito', carritoController::class . ':validarAgregarCarrito');
    });


    //Grupo para productos derivados manzana
    $app->group('/derivadosManzana', function (Group $group) {
        //ruta para ingresar un producto derivado de manzana
        $group->post('/registrar', derivadosManzanaController::class . ':validarIngresarDerivadoManzana');
        //ruta para listar los productos derivados de manzana
        $group->post('/lista', derivadosManzanaController::class . ':validarListaDerivadosManzana');
        //ruta para buscar un producto derivado de manzana
        $group->post('/buscar', derivadosManzanaController::class . ':validarBuscarDerivadoManzanaPorId');
        //ruta para actualizar un producto derivado de manzana
        $group->post('/actualizar', derivadosManzanaController::class . ':validarActualizarDeribadoManzana');
        //ruta para eliminar producto derivado de manzana
        $group->post('/eliminar', derivadosManzanaController::class . ':validarEliminarDerivadoManzana');
    }); 



     //Grupo para Eventos
     $app->group('/Eventos', function (Group $group) {
        //ruta para ingresar un evento
        $group->post('/registrar', eventoController::class . ':validarIngresarEvento');
        //ruta para listar los eventos
        $group->post('/lista', eventoController::class . ':validarListaEventos');
        //ruta para buscar un evento
        $group->post('/buscar', eventoController::class . ':validarBuscarEventoId');
        //ruta para actualizar un evento
        $group->post('/actualizar', eventoController::class . ':validarActualizarEvento');
        //ruta para eliminar un evento
        $group->post('/eliminar', eventoController::class . ':validarEliminarEvento');
    });





    // Grupo para manzanas
    $app->group('/Manzana', function (Group $group) {
        //ruta para ingresar una manzana
        $group->post('/registrar', manzanaController::class . ':validarIngresarManzanas');
        //ruta para listar las manzanas
        $group->post('/lista', manzanaController::class . ':validarListaManzanas');
        //ruta para buscar una manzana
        $group->post('/buscar', manzanaController::class . ':validarBuscarManzanaPorId');
        //ruta para actualizar una manzana
        $group->post('/actualizar', manzanaController::class . ':validarActualizarManzanas');
        //ruta para eliminar una manzana
        $group->post('/eliminar', manzanaController::class . ':validarEliminarManzana');
    });


    //Grupo para puntos de venta
    $app->group('/puntoVenta', function (Group $group) {
        //ruta para ingresar un punto de venta
        $group->post('/registrar', puntoVentaController::class . ':validarIngresarPuntoVenta');
        //ruta para listar los puntos de venta
        $group->post('/lista', puntoVentaController::class . ':validarListaPuntosVenta');
        //ruta para buscar un punto de venta
        $group->post('/buscar', puntoVentaController::class . ':validarBuscarPuntoVentaPorId');
        //ruta para actualizar un punto de venta
        $group->post('/actualizar', puntoVentaController::class . ':validarActualizarPuntoVenta');
        //ruta para eliminar un punto de venta
        $group->post('/eliminar', puntoVentaController::class . ':validarEliminarPuntoVenta');
    });


    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
        $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
        return $handler($req, $res);
    });


};
