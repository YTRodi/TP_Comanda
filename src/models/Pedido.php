<?php 
// Mismo nombre que la tabla que voy a manejar

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model {

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }

    public static function generateUniqueCode() {
        $strToShuffle = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr( str_shuffle( $strToShuffle ), 0, 5 );
    }
}

