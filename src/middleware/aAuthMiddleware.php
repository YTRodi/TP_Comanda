<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


use App\Controllers\AuthJWT;
use Exception;

class AuthMiddleware {

    public $rolUsuario;

    public function __construct( $rolUsuario ) { $this->rolUsuario = $rolUsuario; }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        
        try {
            
            $tokenHeader = $request->getHeader( 'token' )[0]; // string
            // var_dump( $request->getHeader('token') );

            if ( !$tokenHeader ) {

                $response = new Response();
                $response->getBody()->write( 'TOKEN INVALIDO - USTED NO ES ' .  strtoupper( $this->rolUsuario ) );
                
                return $response->withStatus( 401 );

            } else {

                $jwtDecodificado = AuthJWT::ValidarToken( $tokenHeader );
                $tipoUsuario = $jwtDecodificado->data->sector; // string

                if ( $this->rolUsuario !== $tipoUsuario ) {

                    $response = new Response();
                    $response->getBody()->write( 'Prohibido pasar' );
                    
                    return $response->withStatus( 403 );

                } else {

                    $response = $handler->handle( $request );
                    $existingContent = ( string ) $response->getBody();
                    $resp = new Response();
                    $resp->getBody()->write(  $existingContent );

                    return $resp;
                }

            }
            

        } catch (\Throwable $e) {

            throw new Exception( $e->getMessage() );

        }
    }

}