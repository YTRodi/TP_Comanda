<?php

// Slim

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

// Controllers
use Config\Database;
use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\PedidoController;
use App\Controllers\PreparacioneController;


// Middleware
use App\Middleware\JsonMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\AuthAllMiddleware;

require __DIR__ . '/../vendor/autoload.php';


$conn = new Database;

$app = AppFactory::create();
$app->setBasePath( '/TP_Comanda/public' );
const ARRAY_ROLES = [ 'admin', 'cocina', 'barra', 'cerveza', 'mozo']; // Para dar de alta preparaciones


// - Usuarios -
$app->group( '/usuarios', function ( RouteCollectorProxy $group ) {

    $group->get( '[/]', UsuarioController::class . ':getAllUsers' );
    $group->post( '[/]', UsuarioController::class . ':addUser' );
    $group->delete( '/{id}', UsuarioController::class . ':deleteUser' );

})->add( new JsonMiddleware );

$app->post( '/login[/]', UsuarioController::class . ':loginUser' );


// - Productos -
$app->get( '/productos', ProductoController::class . ':getAllProductos' )
      ->add( new AuthMiddleware( 'admin' ) )
      ->add( new JsonMiddleware );



// - Pedidos -
$app->group( '/pedidos', function ( RouteCollectorProxy $group ) {

    $group->get( '[/]', PedidoController::class . ':getAllPedidos' )->add( new AuthMiddleware( 'admin' ) );
    $group->get( '/{codigo}', PedidoController::class . ':getPedidoByCode' );
    $group->put( '/{codigo}', PedidoController::class . ':updatePedido' )->add( new AuthAllMiddleware( ARRAY_ROLES ) );
    $group->post( '[/]', PedidoController::class . ':addPedido' )->add( new AuthMiddleware( 'mozo' ) );



    $group->delete( '/{id}', PedidoController::class . ':deletePedido' );

})->add( new JsonMiddleware );


// - Preparaciones -
$app->group( '/preparaciones', function ( RouteCollectorProxy $group ) {

    $group->get( '/{codigo}', PreparacioneController::class . ':getPreparacionesByCode' )->add( new AuthAllMiddleware( ARRAY_ROLES ) );
    $group->put( '/{codigo}', PreparacioneController::class . ':updatePreparacion' )->add( new AuthAllMiddleware( ARRAY_ROLES ) );

})->add( new JsonMiddleware );


// $app->group( '/users', function ( RouteCollectorProxy $group ) {

//     // Traer todos
//     $group->get( '[/]', UsuarioController::class . ':getAllUsers' );

//     // Punto 1
//     $group->post( '[/]', UsuarioController::class . ':addUser' );

// })->add( new JsonMiddleware );


// // Punto 2
// $app->post( '/login[/]', UsuarioController::class . ':loginUser' );


// // Punto 3
// $app->post( '/materia[/]', MateriaController::class . ':addMateria' )
//     ->add( new AuthMiddleware( 'admin' ) )
//     ->add( new JsonMiddleware );

// // Punto 4
// $app->post( '/inscripcion/{id}', InscripcionController::class . ':addInscripcion' )
//     ->add( new AuthMiddleware( 'alumno' ) );


// // Punto 5
// $app->put( '/notas/{id}', InscripcionController::class . ':addNotaAlumno' )
//     ->add( new AuthMiddleware( 'profesor' ) );


// // Punto 7
// $app->get( '/materia[/]', MateriaController::class . ':getAllMaterias' )
//     ->add( new JsonMiddleware );



$app->addBodyParsingMiddleware(); // Para poder usar los datos que enviamos desde el body para el PUT ( vamos por 'x-www-form-urlencoded', no por form-data)
$app->run();