<?php


use Config\Database;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

// Controllers
use Controllers\UsuarioController;

// Middleware
use Middleware\JsonMiddleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$conn = new Database();

const ARRAY_ROLES = [ 'admin', 'cocina', 'barra', 'cerveza', 'mozo']; // Para dar de alta preparaciones

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write( "Hello world" );
    return $response;
});


// - Usuarios -
$app->group('/usuarios', function ( RouteCollectorProxy $group ) {

    $group->get('[/]', UsuarioController::class . ':getAllUsers' );
    $group->post('[/]', UsuarioController::class . ':addUser' );
    $group->delete('/{id}', UsuarioController::class . ':deleteUser' );

})->add( new JsonMiddleware );

// // - Login -
// $app->post( '/login[/]', UsuarioController::class . ':loginUser' );


// // - Productos -
// $app->get( '/productos', ProductoController::class . ':getAllProductos' )
//       ->add( new AuthMiddleware( 'admin' ) )
//       ->add( new JsonMiddleware );



// // - Pedidos -
// $app->group( '/pedidos', function ( RouteCollectorProxy $group ) {

//     $group->get( '[/]', PedidoController::class . ':getAllPedidos' )->add( new AuthMiddleware( 'admin' ) );
//     $group->get( '/{codigo}', PedidoController::class . ':getPedidoByCode' );
//     $group->put( '/{codigo}', PedidoController::class . ':updatePedido' )->add( new AuthAllMiddleware( ARRAY_ROLES ) );
//     $group->post( '[/]', PedidoController::class . ':addPedido' )->add( new AuthMiddleware( 'mozo' ) );
//     // $group->delete( '/{id}', PedidoController::class . ':deletePedido' );

// })->add( new JsonMiddleware );


// // - Preparaciones -
// $app->group( '/preparaciones', function ( RouteCollectorProxy $group ) {

//     $group->get( '/{codigo}', PreparacioneController::class . ':getPreparacionesByCode' )->add( new AuthAllMiddleware( ARRAY_ROLES ) );
//     $group->put( '/{codigo}', PreparacioneController::class . ':updatePreparacion' )->add( new AuthAllMiddleware( ARRAY_ROLES ) );

// })->add( new JsonMiddleware );


// // - Mesas -
// $app->group( '/mesas', function ( RouteCollectorProxy $group ) {

//     // TODO
//     // GET RECAUDACIÓN DE DINERO DE LAS MESAS????

//     $group->get( '[/]', MesaController::class . ':getAllMesas' )->add( new AuthMiddleware( 'admin' ) );
//     $group->get( '/{codigo}', MesaController::class . ':getMesaByCode' );

//     $group->put( '/comiendo/{codigo}', MesaController::class . ':updateMesaEating' )->add( new AuthMiddleware( 'mozo' ) );
//     $group->put( '/pagando/{codigo}', MesaController::class . ':updateMesaPaying' )->add( new AuthMiddleware( 'mozo' ) );;
//     $group->put( '/cerrando/{codigo}', MesaController::class . ':updateMesaClosing' )->add( new AuthMiddleware( 'admin' ) );;

//     $group->post( '[/]', MesaController::class . ':addMesa' )->add( new AuthMiddleware( 'admin' ) );

// })->add( new JsonMiddleware );


// $app->addBodyParsingMiddleware(); // Para poder usar los datos que enviamos desde el body para el PUT ( vamos por 'x-www-form-urlencoded', no por form-data)
$app->run();