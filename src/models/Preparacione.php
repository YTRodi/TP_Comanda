<?php 
// Mismo nombre que la tabla que voy a manejar

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Preparacione extends Model {

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
}

