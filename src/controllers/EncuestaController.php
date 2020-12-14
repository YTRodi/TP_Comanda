<?php 

namespace Controllers;

use Models\Encuesta;
use Models\Mesa;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EncuestaController {

    public $comentarios = [
        'Excelente servicio',
        'Pesimo servicio',
        'Servicio regular',
        'La comida estuvo nefasta',
        'No vuelvo nunca más',
        'Sin comentarios',
        'Todo perfecto',
        'Restaurante altamente recomendado'
    ];

    public function addEncuesta ( Request $request, Response $response ) {

        $encuesta = new Encuesta;
        $encuesta[ 'id_mesa' ] = $request->getParsedBody()[ 'id_mesa' ];
        $mesaById = Mesa::get()->where( 'id', '=', $encuesta[ 'id_mesa' ] )->first();

        if( $mesaById ) {

            if( $mesaById['estado'] === 'cerrada' ) {

                $encuesta[ 'id_mesa' ] = $mesaById[ 'id' ];
                $encuesta[ 'puntaje_mesa' ] = $request->getParsedBody()[ 'puntaje_mesa' ] || 0;
                $encuesta[ 'puntaje_restaurante' ] = $request->getParsedBody()[ 'puntaje_restaurante' ] || 0;
                $encuesta[ 'puntaje_mozo' ] = $request->getParsedBody()[ 'puntaje_mozo' ] || 0;
                $encuesta[ 'puntaje_cocinero' ] = $request->getParsedBody()[ 'puntaje_cocinero' ] || 0;
                $randomKeys = array_rand( $this->comentarios ); // Genero un índice aleatorio
                $encuesta[ 'comentario' ] = $this->comentarios[ $randomKeys ];

                $rta = $encuesta->save();

                if( $rta ){
                    $response->withStatus(201)->getBody()->write( 'Encuesta realizada con éxito' );
                }

            } else {

                $response->getBody()->write( 'La mesa todavía no está cerrada.' );

            }
            

        } else {

            $response->getBody()->write( 'No se encontró la mesa.' );
            
        }

        return $response;
        
    }

}