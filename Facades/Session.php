<?php

namespace Fmk\Facades;

use Fmk\Interfaces\Auth;
use Fmk\Traits\Singleton;

class Session
{
    use Singleton;

    const DATA_KEY = 'data';
    const USER_KEY = 'user';
    const REQUEST_KEY = 'request';

    const REQUEST_OLD_KEY = 'request_old';


    final private function __construct()
    {
        if (defined('SESSION_NAME')) {
            session_name(constant('SESSION_NAME'));
        } elseif (defined('APPLICATION_NAME')) {
            session_name(urlencode(constant('APPLICATION_NAME')));
        }
        session_start();
        $this->init();
    }

    public function init(){
        if(!array_key_exists(static::DATA_KEY, $_SESSION)){
            $_SESSION[static::DATA_KEY] = [];
        }
    }

   

    public function __get($name)
    {
        return $_SESSION[static::DATA_KEY][$name] ?? null;
    }
    public function __isset($name)
    {
        return isset($_SESSION[static::DATA_KEY][$name]);
    }
    public function __unset($name)
    {
        unset($_SESSION[static::DATA_KEY][$name]);
    }

    public function __set($name, $value){
            $_SESSION[static::DATA_KEY][$name] = $value;
    }

    /**
     * Redireciona chamadas de métodos estáticos para a instância singleton
     * @param mixed $name
     * @param mixed $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::getInstance(), $name], $arguments);

    }

    /**
     * Redireciona chamadas de métodos não estáticos para a instância singleton
     * @param mixed $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([static::getInstance(), $name], $arguments);
    }

    /**
     * Retorna todos os dados armazenados na sessão
     */
    protected function all()
    {
        return $_SESSION[static::DATA_KEY];
    }

    /**
     * Metodo para obter o valor de uma chave e removê-la da sessão
     * @param mixed $name
     */
    protected function flush($name){
        $flush = $this->$name;
        unset($this->$name);
        return $flush;
    }
    protected function userRegister(Auth $user){
        if(!$this->userIsRegister()){
            $_SESSION[static::USER_KEY] = $user;
            return true;
        }
        return false;
    }

    protected function userIsRegister(){
        return isset($_SESSION[static::USER_KEY]);
    }

    protected function userUnRegister(){
        if($this->userIsRegister()){
            $user =$_SESSION[self::USER_KEY];
            unset($_SESSION[self::USER_KEY]);
            return $user;
        }
        return false;
    }

    protected function requestRegister(){
        $_SESSION[static::REQUEST_OLD_KEY] = $this->request();
        $_SESSION[static::REQUEST_KEY] = Request::toArray();
    }

    protected function request(){
        return $_SESSION[static::REQUEST_KEY] ?? null;
    }
    protected function request_old(){
        return $_SESSION[static::REQUEST_OLD_KEY] ?? null;
    }


}
