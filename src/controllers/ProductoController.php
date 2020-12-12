<?php 

namespace Controllers;

use Models\Producto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ProductoController {

    public function getAllProductos ( Request $request, Response $response ) {
    
        $rta = Producto::get();     

        $response->getBody()->write( json_encode( $rta ) );

        return $response;

    }
    
}