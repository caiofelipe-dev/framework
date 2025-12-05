<?php

namespace Fmk\Facades\Database;

use Exception;
use Fmk\Facades\Config;

class DB{
    protected $parameters;
 
    public function __construct($connection_name){
        // Nome do arquivo de configuração que contém conexões de banco.
        // Usa a constante `DATABASE_CONFIG_FILE` se estiver definida, caso contrário 'database'.
        $database_file = defined('DATABASE_CONFIG_FILE') ? constant('DATABASE_CONFIG_FILE') : 'database';
        $parameters = Config::get($database_file.".".$connection_name);
        if(!isset($parameters['driver'])){
            throw new Exception("Driver é um parametro obrigatório para estabelecer uma conexão com a base de dados");
        }

        if(!array_key_exists($parameters['driver'], constant('DATABASE_DRIVERS'))){
            throw new Exception("Driver {$parameters['driver']} não é suportado nessa versão do framework");
        }
        $this->parameters = $parameters;
    }

    public function getDriver(){
        $drivers = constant('DATABASE_DRIVERS');
        $drive = $drivers[$this->parameters['driver']];
        return new $drive($this->parameters);
    }

    public function table($table_name){
        return new Query($this->getDriver(), $table_name);
    }

    public function exec(string $sql,array $data = []){
        return $this->getDriver()->execute($sql,$data);
    }

}