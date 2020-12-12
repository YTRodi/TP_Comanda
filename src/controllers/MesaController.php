<?php 

namespace Controllers;

use Models\Mesa;
use Models\Pedido;
use Controllers\AuthJWT;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


use Exception;

class MesaController {

    public function getAllMesas ( Request $request, Response $response ) {
    
        $rta = Mesa::get();

        $response->getBody()->write( json_encode( $rta ) );

        return $response;

    }

    public function getMesaByCode ( Request $request, Response $response, $args ) {
    
        // Lógica: con el código me traigo el pedido específico.
        $codigoMesa = $args['codigo'];
        $mesaByCode = Mesa::get()->where( 'codigo', '=', $codigoMesa )->first();

        if( $mesaByCode )
            $response->getBody()->write( json_encode( $mesaByCode ));
        else 
            $response->getBody()->write( 'No existe el pedido con el código ' . $codigoMesa );

        return $response;

    }

    public function addMesa ( Request $request, Response $response ) {

        $mesa = new Mesa;
        $codigo = Mesa::generateUniqueCode();

        if( !Mesa::where( 'codigo', $codigo )->exists() ) {
            $mesa['codigo'] = $codigo;
            $mesa['estado'] = $request->getParsedBody()['estado'] ?? '';
            
            $rta = $mesa->save();
            $response->getBody()->write( json_encode( $rta ) );
            
        }

        return $response;
        
    }

    public function updateMesaEating ( Request $request, Response $response, $args ) {

        try {

            // Lógica: traer el pedido que este asignado a esta mesa.
            $codigoMesa = $args['codigo'];
            $mesaByCode = Mesa::get()->where( 'codigo', '=', $codigoMesa )->first();
            $pedido = Pedido::get()->where( 'codigo_mesa', '=', $codigoMesa )->first();

            if( $pedido['estado_general'] === 'listo' ) {

                $mesaByCode['estado'] = 'con clientes comiendo';
                $rta = $mesaByCode->save();
                $response->getBody()->write( 'Se actualizó el estado de la mesa a: ' . $mesaByCode['estado'] );

            } else 
                $response->getBody()->write( 'El pedido todavía no está listo.' );
            
            return $response;

        } catch (\Throwable $e) {

            throw new Exception( $e->getMessage() );

        }
        
    }

    public function updateMesaPaying ( Request $request, Response $response, $args ) {

        try {

            // Lógica: traer el pedido que este asignado a esta mesa.
            $codigoMesa = $args['codigo'];
            $mesaByCode = Mesa::get()->where( 'codigo', '=', $codigoMesa )->first();
            $pedido = Pedido::get()->where( 'codigo_mesa', '=', $codigoMesa )->first();

            if( $mesaByCode['estado'] === 'con clientes comiendo' ) {

                $mesaByCode['estado'] = 'con clientes pagando';
                $rta = $mesaByCode->save();
                $response->getBody()->write( 'Se actualizó el estado de la mesa a: ' . $mesaByCode['estado'] );

            } else 
                $response->getBody()->write( 'El pedido todavía no está listo.' );
            
            return $response;

        } catch (\Throwable $e) {

            throw new Exception( $e->getMessage() );

        }
        
    }

    public function updateMesaClosing ( Request $request, Response $response, $args ) {

        try {

            // Lógica: traer el pedido que este asignado a esta mesa.
            $codigoMesa = $args['codigo'];
            $mesaByCode = Mesa::get()->where( 'codigo', '=', $codigoMesa )->first();
            $pedido = Pedido::get()->where( 'codigo_mesa', '=', $codigoMesa )->first();

            if( $mesaByCode['estado'] === 'con clientes pagando' ) {

                $mesaByCode['estado'] = 'cerrada';
                $rta = $mesaByCode->save();
                $response->getBody()->write( 'Se actualizó el estado de la mesa a: ' . $mesaByCode['estado'] );

            } else 
                $response->getBody()->write( 'El pedido todavía no está listo.' );
            
            return $response;

        } catch (\Throwable $e) {

            throw new Exception( $e->getMessage() );

        }
        
    }

}