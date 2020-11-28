<?php 

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Pedido;
use App\Models\Preparacione;
use App\Models\Producto;
use App\Models\Usuario;
use stdClass;

class PedidoController {

    public function getAllPedidos ( Request $request, Response $response ) {

        $rta = Pedido::get();     
        $obj = new stdClass;
        $obj->nombre = 'pepe';
        $obj->preparaciones = ['milanesa','agua mineral'];
        // $response->getBody()->write( json_encode( $rta ) );
        $response->getBody()->write( json_encode( $obj ));

        return $response;

    }

    public function getPedidoByCode ( Request $request, Response $response ) {
    
        // Lógica: con el código me traigo el pedido específico.
        //pedido.codigo === $args['pedido']; algo asi...


        // $rta = Pedido::get();     
        // $obj = new stdClass;
        // $obj->nombre = 'pepe';
        // $obj->preparaciones = ['milanesa','agua mineral'];
        // // $response->getBody()->write( json_encode( $rta ) );
        // $response->getBody()->write( json_encode( $obj ));

        return $response;

    }


    public function addPedido ( Request $request, Response $response ) {

        // echo json_encode( $preparaciones );
        $preparaciones = $request->getParsedBody()['preparaciones'];
        
        // Por cada preparación (del pedido) creo una nueva preparación.
        foreach ($preparaciones as $item) {

            // echo $item['id_producto'] . PHP_EOL;
            $idProducto = $item[ 'id_producto' ];
            $cantidadProducto = $item[ 'cantidad' ];

            $nuevaPreparacion = new Preparacione;
            $nuevaPreparacion[ 'codigo_pedido' ] = $request->getParsedBody()[ 'codigo' ] ?? '';

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


        // $pedido = new Pedido;
        // $pedido['codigo'] = $request->getParsedBody()['codigo'] ?? ''; // GENERAR AUTOMATICAMENTE ACÁ
        // $pedido['id_mesa'] = intval( $request->getParsedBody()['id_mesa'] ?? '' );
        // $pedido['id_mozo'] = intval( $request->getParsedBody()['id_mozo'] ?? '' );
        // $pedido['nombre_cliente'] = $request->getParsedBody()['nombre_cliente'] ?? '';
        // $pedido['tiempo_espera'] = intval( $request->getParsedBody()['tiempo_espera'] ?? '' );
        // $pedido['estado'] = "en preparacion";
        // $pedido['preparaciones'] = $request->getParsedBody()['preparaciones']; // id de los productos
        // echo json_encode( $pedido );


        // $rta = $pedido->save();
        // $response->getBody()->write( json_encode( $rta ) );

        return $response;
        
    }

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