<?php
// Este archivo hace la conexión a la base de datos.
namespace Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Database {
    
    public function __construct() {

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'bnqbjabqekwmoupl8t93-mysql.services.clever-cloud.com',
            'database'  => 'bnqbjabqekwmoupl8t93',
            'username'  => 'u3u8mcep1vn0irkg',
            'password'  => 'tz4BQqi9l0TOm7A6peqE',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        // Set the event dispatcher used by Eloquent models... (optional)

        $capsule->setEventDispatcher(new Dispatcher(new Container));

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
        
    }

}