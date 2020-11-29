<?php

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model {

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
}


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class UsuarioController {

    // JWT
    public function loginUser ( Request $request, Response $response ) {

        //Username, password y sector
        $body = $request->getParsedBody();
        $username = $body['username'];
        $password = $body['password'];
        $sector = $body['sector'];

        $user = Usuario::where('username','=',$username)
                       ->where('password','=',$password)
                       ->where('sector','=',$sector)
                       ->first();

        if ( $user ) {

                $payload = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'password' => $user['password'],
                    'sector' => $user['sector']
                ];

                $token = AuthJWT::Login( $payload );

                // TOKEN!!!
                print_r( $token );

        } else {

            $response->getBody()->write( 'No existe el usuario.' );

        }

        return $response;

    }

    // -------------------------------------------------------------------
    // -------------------------------------------------------------------
    // -------------------------------------------------------------------

    // Funciones CRUD
    public function getAllUsers ( Request $request, Response $response ) {
    
        $rta = Usuario::get();     

        $response->getBody()->write( json_encode( $rta ) );

        return $response;

    }

    public function getOneUser ( Request $request, Response $response, $args ) {
    
        $rta = Usuario::find( $args['id'] ); // pq no use el int val?

        $response->getBody()->write( json_encode( $rta ) );

        return $response;
        
    }

    public function addUser ( Request $request, Response $response ) {

        $user = new Usuario;
        $user['username'] = $request->getParsedBody()['username'] ?? '';
        $user['password'] = $request->getParsedBody()['password'] ?? '';
        $user['estado'] = $request->getParsedBody()['estado'] ?? '';
        $user['sector'] = $request->getParsedBody()['sector'] ?? '';
        $user['operaciones'] = $request->getParsedBody()['operaciones'] ?? '';
        // echo json_encode($user);

        $rta = $user->save();
        $response->getBody()->write( json_encode( $rta ) );

        return $response;
        
    }

    //! PARA CAMBIAR EL ESTADO! ( SÓLO ADMIN )
    // public function updateUser ( Request $request, Response $response, $args ) {
    
    //     try {
            
    //         $idUrl = $args['id'] ?? '';
    //         $user = Usuario::find( intval( $idUrl ) );

    //         $rta = $user->save();
    //         $response->getBody()->write( json_encode( $rta ) );
    //         return $response;

    //     } catch (\Throwable $e) {

    //         throw new Exception( $e->getMessage() );

    //     }
        
    // }

    public function deleteUser ( Request $request, Response $response, $args ) {
    
        $idUrl = $args['id'] ?? '';
        $user = Usuario::find( intval( $idUrl ) );
        // echo json_encode($user);

        if( $user ) {

            $rta = $user->delete();
            $response->getBody()->write( json_encode( $rta ) );
            
        } else {
            $response->getBody()->write( 'No existe usuario con el id = ' . $idUrl );
        }

        return $response;
        
    }

    // -------------------------------------------------------------------
    // -------------------------------------------------------------------
    // -------------------------------------------------------------------

    // Funciones CRUD
    // public function getAllUsers ( Request $request, Response $response ) {
    
    //     $rta = Usuario::get();     

    //     $response->getBody()->write( json_encode( $rta ) );

    //     return $response;

    // }

    // public function getOneUser ( Request $request, Response $response, $args ) {
    
    //     $rta = Usuario::find( $args['id'] );

    //     $response->getBody()->write( json_encode( $rta ) );

    //     return $response;
        
    // }

    // public function addUser ( Request $request, Response $response ) {

    //     // var_dump($request->getParsedBody());

    //     $user = new Usuario;
    //     $user->clave = $request->getParsedBody()['clave'] ?? '';
    //     $user->email = $request->getParsedBody()['email'] ?? '';
    //     $user->tipo = $request->getParsedBody()['tipo'] ?? '';
    //     // echo $user->$clave . '<br/>';
    //     // echo $user->$email . '<br/>';
    //     // echo $user->$tipo . '<br/>';

    //     $rta = $user->save();
    //     $response->getBody()->write( json_encode( $rta ) );

    //     return $response;
        
    // }

    // public function updateUser ( Request $request, Response $response, $args ) {
    
    //     // var_dump( $args['id'] );
    //     $idUrl = $args['id'] ?? '';
    //     $user = Usuario::find( intval( $idUrl ) );
    //     // echo json_encode( $user );

    //     // Hago las modicaciones y vuelvo a guardar
    //     $user->clave = $request->getParsedBody()['clave'] ?? '';
    //     $user->email = $request->getParsedBody()['email'] ?? '';
    //     $user->tipo = $request->getParsedBody()['tipo'] ?? '';
    
    //     $rta = $user->save();
    //     $response->getBody()->write( json_encode( $rta ) );
        
    //     return $response;
        
        
    // }

    // public function deleteUser ( Request $request, Response $response, $args ) {
    
    //     $idUrl = $args['id'] ?? '';
    //     $user = Usuario::find( intval( $idUrl ) );
    //     echo json_encode($user);


    //     // $rta = $user->delete();
    //     // $response->getBody()->write( json_encode( $rta ) );
        
    //     return $response;
        
    // }
    
}

use App\Controllers\MesaController;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

// Controllers
// use Config\Database;
// use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\PedidoController;
use App\Controllers\PreparacioneController;


// Middleware
// use App\Middleware\JsonMiddleware;
// use App\Middleware\AuthMiddleware;
// use App\Middleware\AuthAllMiddleware;

// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';


// $conn = new Database;

$app = AppFactory::create();
// $app->setBasePath( '/TP_Comanda/public' );
const ARRAY_ROLES = [ 'admin', 'cocina', 'barra', 'cerveza', 'mozo']; // Para dar de alta preparaciones

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write( "Hello world" );
    return $response;
});

// - Usuarios -
$app->group( '/usuarios', function ( RouteCollectorProxy $group ) {

    $group->get( '[/]', UsuarioController::class . ':getAllUsers' );
    $group->post( '[/]', UsuarioController::class . ':addUser' );
    $group->delete( '/{id}', UsuarioController::class . ':deleteUser' );

});





// // - Usuarios -
// $app->group( '/usuarios', function ( RouteCollectorProxy $group ) {

//     $group->get( '[/]', UsuarioController::class . ':getAllUsers' );
//     $group->post( '[/]', UsuarioController::class . ':addUser' );
//     $group->delete( '/{id}', UsuarioController::class . ':deleteUser' );

// })->add( new JsonMiddleware );

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
//     // $group->get( '[/]', MesaController::class . ':getAllMesas' )->add( new AuthMiddleware( 'admin' ) );
//     $group->get( '/{codigo}', MesaController::class . ':getMesaByCode' );
//     $group->put( '/{codigo}', MesaController::class . ':updateMesa' );
//     $group->post( '[/]', MesaController::class . ':addMesa' )->add( new AuthMiddleware( 'admin' ) );

// })->add( new JsonMiddleware );


// $app->addBodyParsingMiddleware(); // Para poder usar los datos que enviamos desde el body para el PUT ( vamos por 'x-www-form-urlencoded', no por form-data)
$app->run();