<?php 

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Usuario;

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