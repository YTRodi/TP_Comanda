<?php

namespace Middleware;

use Controllers\AuthJWT;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use Exception;

class AuthAllMiddleware {

    public $arrayRoles;

    public function __construct( $arrayRoles ) { $this->arrayRoles = $arrayRoles; }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        
        try {
            
            $tokenHeader = $request->getHeader( 'token' )[0]; // string

            if ( !$tokenHeader ) {

                $response = new Response();
                $response->getBody()->write( 'TOKEN INVALIDO' );
                
                return $response->withStatus( 401 );

            } else {

                $jwtDecodificado = AuthJWT::ValidarToken( $tokenHeader );
                $tipoUsuario = $jwtDecodificado->data->sector; // string
                // echo $tipoUsuario . '<br/>';
                $distintoTipo = false;

                for ($i=0; $i < count( $this->arrayRoles ); $i++) { 

                    // echo $this->arrayRoles[ $i ] .'<br/>';
                    if ( $this->arrayRoles[ $i ] !== $tipoUsuario ) {

                        $distintoTipo = true;

                    } else if ( $this->arrayRoles[ $i ] === $tipoUsuario ){

                        $response = $handler->handle( $request );
                        $existingContent = ( string ) $response->getBody();
                        $resp = new Response();
                        $resp->getBody()->write(  $existingContent );
                        
                        return $resp;
                    }

                }

                if ( $distintoTipo ) { 

                    $response = new Response();
                    $response->getBody()->write( 'Prohibido pasar' );
                    
                    return $response->withStatus( 403 );

                }

            }
            

        } catch (\Throwable $e) {

            throw new Exception( $e->getMessage() );

        }
    }

}