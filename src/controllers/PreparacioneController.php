<?php 

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Preparacione;

class PreparacioneController {

    public function getAllPreparaciones ( Request $request, Response $response ) {
    
        // Lógica: traigo todas las preparaciones
        $rta = Preparacione::get();     
        $response->getBody()->write( json_encode( $rta ) );

        return $response;

    }

    public function getPreparacionesByCode ( Request $request, Response $response, $args ) {
    
        // Lógica: con el código me traigo las preparaciones existentes con el código de pedido que me dan.
        $codigoPedido = $args['codigo'];
        $rta = Preparacione::where( 'codigo_pedido', '=', $codigoPedido )->get();
        $response->getBody()->write( json_encode( $rta ) );

        return $response;
    }

}