<?php 

namespace Controllers;

use Models\Mesa;
use Models\Pedido;
use Models\Preparacione;
use Models\Producto;
use Models\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

class PedidoController {

    public function getAllPedidos ( Request $request, Response $response ) {

        $rta = Pedido::get();     
        $response->getBody()->write( json_encode( $rta ) );

        return $response;

    }

    public function getPedidoByCode ( Request $request, Response $response, $args ) {
    
        // Lógica: con el código me traigo el pedido específico.
        $codigoPedido = $args['codigo'];
        $pedidoByCode = Pedido::get()->where( 'codigo', '=', $codigoPedido )->first();

        if( $pedidoByCode )
            $response->getBody()->write( json_encode( $pedidoByCode ));
        else 
            $response->getBody()->write( 'No existe el pedido con el código ' . $codigoPedido );

        return $response;

    }


    public function addPedido ( Request $request, Response $response ) {

        $codigo = Pedido::generateUniqueCode();
        $preparaciones = $request->getParsedBody()['preparaciones'];
        
        // Por cada preparación (del pedido) creo una nueva preparación.
        foreach ($preparaciones as $item) {

            $idProducto = $item[ 'id_producto' ];
            $cantidadProducto = $item[ 'cantidad' ];

            $nuevaPreparacion = new Preparacione;
            $nuevaPreparacion[ 'codigo_pedido' ] = $codigo;

            // Matcheo el id del producto con el objeto.
            $producto = Producto::get()
                ->where( 'id', '=', $idProducto )
                ->first();
            // echo $producto;


            // Matcheo el sector del producto con el primer empleado disponible a prepararlo.
            $empleado = Usuario::get()
                ->where( 'sector', '=', $producto['sector'] )
                ->where( 'estado', '=', 'servicio' )
                ->first();
            // echo $empleado;
            
            $nuevaPreparacion[ 'id_usuario' ] = $empleado['id'];
            $nuevaPreparacion[ 'id_producto' ] = $producto['id'];
            $nuevaPreparacion[ 'cantidad' ] = $cantidadProducto;
            // echo json_encode( $nuevaPreparacion );

            $rtaPreparacion = $nuevaPreparacion->save();
            $response->getBody()->write( json_encode( $rtaPreparacion ) );

        }

        $pedido = new Pedido;
        $codigoMesa = $request->getParsedBody()['codigo_mesa'] ?? '';
        $mesa = Mesa::get()->where( 'codigo', $codigoMesa )->first();

        // Si no existe pedido con este código, puedo crearlo.
        if( !Pedido::where( 'codigo', $codigo )->exists() ) {

            $pedido['codigo'] = $codigo;
            $pedido['codigo_mesa'] = $codigoMesa;
            $pedido['id_mozo'] = intval( $request->getParsedBody()['id_mozo'] ?? '' );
            $pedido['nombre_cliente'] = $request->getParsedBody()['nombre_cliente'] ?? '';
            $pedido['tiempo_espera'] = intval( $request->getParsedBody()['tiempo_espera'] ?? '' );
            $pedido['estado_general'] = $request->getParsedBody()['estado_general'] ?? '';
            $pedido['estado_cocina'] = $request->getParsedBody()['estado_cocina'] ?? '';
            $pedido['estado_barra'] = $request->getParsedBody()['estado_barra'] ?? '';
            $pedido['estado_cerveza'] = $request->getParsedBody()['estado_cerveza'] ?? '';
                
            $rta = $pedido->save();
            $response->getBody()->write( json_encode( $rta ) );

            // Le cambio el estado a la mesa.
            $mesa['estado'] = 'con cliente esperando pedido';

            $rta = $mesa->save();
            $response->getBody()->write( json_encode( $rta ) );

        }
        return $response;
        
    }


    public function updatePedido ( Request $request, Response $response, $args ) {
    
        try {

            $codigoPedido = $args['codigo'];
            $pedido = Pedido::get()->where( 'codigo', '=', $codigoPedido )->first();
            // Traigo todas las preparaciones con el código especificado.
            $preparacionesByCode = Preparacione::where( 'codigo_pedido', '=', $codigoPedido )->get();

            if( $pedido['tiempo_espera'] != 0 ) {

                foreach ( $preparacionesByCode as $key => $preparacion ) {

                    // Lógica: Si el usuario está en la preparación, le cambio el estado al pedido.
                    $idUsuario = $preparacion['id_usuario'];
                    $usuario = Usuario::get()->where( 'id', '=', $idUsuario )->first();
    
                    // Switch para preparación.
                    switch ( $usuario['sector'] ) {
                        case 'cocina':
                            $pedido['estado_cocina'] = 'en preparación';
                            break;
    
                        case 'barra':
                            $pedido['estado_barra'] = 'en preparación';
                            break;
    
                        case 'cerveza':
                            $pedido['estado_cerveza'] = 'en preparación';
                            break;
    
                    }  
                }
                
                $pedido['estado_general'] = 'en preparación';
                // Resto el tiempo de espera para poder avisar que el pedido está listo...
                $pedido['tiempo_espera'] -= $pedido['tiempo_espera'];
                $rta = $pedido->save();
                $response->getBody()->write( json_encode( $rta ) );
                
            } else {
                // echo $pedido['estado_cocina'] . ' ' . $pedido['estado_barra'] . ' ' . $pedido['estado_cerveza'];
                
                // Si el tiempo esperado es -= tiempo esperado => significa que el pedido está listo, por lo tanto le cambio el estado.
                if( $pedido['estado_cocina'] === 'en preparación' ) $pedido['estado_cocina'] = 'listo';
                if( $pedido['estado_barra'] === 'en preparación' ) $pedido['estado_barra'] = 'listo';
                if( $pedido['estado_cerveza'] === 'en preparación' ) $pedido['estado_cerveza'] = 'listo';
                $pedido['estado_general'] = 'listo';
                
                // Guardo el nuevo estado del pedido.
                $rta = $pedido->save();
                $response->getBody()->write( json_encode( $rta ) );

                // Aumentó la cantidad de operaciones al empleado correspondiente.
                foreach ( $preparacionesByCode as $key => $preparacion ) {

                    $idUsuario = $preparacion['id_usuario'];
                    $usuario = Usuario::get()->where( 'id', '=', $idUsuario )->first();

                    $usuario['operaciones'] += $preparacion['cantidad']; // Le sumamos las cantidades que preparó.
                    $rta = $usuario->save();
                }

                
            }
            
            return $response;

        } catch (\Throwable $e) {

            throw new Exception( $e->getMessage() );

        }
        
    }

}