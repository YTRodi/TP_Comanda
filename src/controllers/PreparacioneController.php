<?php 

namespace App\Controllers;

use App\Models\Pedido;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Preparacione;

class PreparacioneController {

    public function getAllPreparaciones ( Request $request, Response $response ) {
    
        // Lógica: traigo todas las preparaciones
        $rta = Preparacione::get();     
        $response->getBody()->write( 'eueueueu' );

        return $response;

    }

    public function getPreparacionesByCode ( Request $request, Response $response ) {
    
        // Lógica: con el código me traigo las preparaciones existentes con el código de pedido que me dan.
        $rta = Preparacione::get();     
        $response->getBody()->write( json_encode( $rta ) );

        return $response;

    }

    public function addPreparacion ( Request $request, Response $response ) {

        // Tengo que traer el pedido que me pasan por el body, tomar solamente las preparaciones y matchear esas preparaciones con los productos de la DB.
        // Una vez que tengo el producto, matcheo el sector del empleado con el tipo del producto.
        // Lógica: Tengo que traer los empleados y productos

        $codigoPedido = $request->getParsedBody()[ 'codigo' ] ?? '';

        $pedido = Pedido::where( 'codigo','=', $codigoPedido )->first();
        echo json_encode($pedido);
        
        // $user = new Preparacione;
        // $user['username'] = $request->getParsedBody()['username'] ?? '';
        // $user['password'] = $request->getParsedBody()['password'] ?? '';
        // $user['estado'] = $request->getParsedBody()['estado'] ?? '';
        // $user['sector'] = $request->getParsedBody()['sector'] ?? '';
        // $user['operaciones'] = $request->getParsedBody()['operaciones'] ?? '';
        // echo json_encode($user);

        // $rta = $user->save();
        // $response->getBody()->write( json_encode( $rta ) );

        return $response;
        
    }
    
    // public function addPreparacion ( Request $request, Response $response ) {

    //     // Tengo que traer el pedido que me pasan por el body, tomar solamente las preparaciones y matchear esas preparaciones con los productos de la DB.
    //     // Una vez que tengo el producto, matcheo el sector del empleado con el tipo del producto.
    //     // Lógica: Tengo que traer los empleados y productos

    //     $codigoPedido = $request->getParsedBody()[ 'codigo' ] ?? '';

    //     $pedido = Pedido::where( 'codigo','=', $codigoPedido )->first();
    //     echo json_encode($pedido);
        
    //     // $user = new Preparacione;
    //     // $user['username'] = $request->getParsedBody()['username'] ?? '';
    //     // $user['password'] = $request->getParsedBody()['password'] ?? '';
    //     // $user['estado'] = $request->getParsedBody()['estado'] ?? '';
    //     // $user['sector'] = $request->getParsedBody()['sector'] ?? '';
    //     // $user['operaciones'] = $request->getParsedBody()['operaciones'] ?? '';
    //     // echo json_encode($user);

    //     // $rta = $user->save();
    //     // $response->getBody()->write( json_encode( $rta ) );

    //     return $response;
        
    // }

    // //! PARA CAMBIAR EL ESTADO! ( SÓLO ADMIN )
    // // public function updateUser ( Request $request, Response $response, $args ) {
    
    // //     try {
            
    // //         $idUrl = $args['id'] ?? '';
    // //         $user = Usuario::find( intval( $idUrl ) );

    // //         $rta = $user->save();
    // //         $response->getBody()->write( json_encode( $rta ) );
    // //         return $response;

    // //     } catch (\Throwable $e) {

    // //         throw new Exception( $e->getMessage() );

    // //     }
        
    // // }

    // public function deleteUser ( Request $request, Response $response, $args ) {
    
    //     $idUrl = $args['id'] ?? '';
    //     $user = Usuario::find( intval( $idUrl ) );
    //     // echo json_encode($user);

    //     if( $user ) {

    //         $rta = $user->delete();
    //         $response->getBody()->write( json_encode( $rta ) );
            
    //     } else {
    //         $response->getBody()->write( 'No existe usuario con el id = ' . $idUrl );
    //     }

    //     return $response;
        
    // }

}