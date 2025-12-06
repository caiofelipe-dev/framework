<?php

namespace Fmk\Facades;

use Exception;
use Fmk\Enums\Methods;
use Fmk\Traits\Middlewares;



class Route
{
    use Middlewares;
    protected $uri;

    protected $callback;

    public array $paramns; //['cod_cliente'=>null]

    protected Methods $method;

    protected string $name;

    protected bool $active = false;


    public function __construct($name, $uri, Methods $method, $callback)
    {
        $this->name = $name;
        $this->uri = $uri;
        $this->method = $method;
        $this->callback = $callback;
        $this->paramns = array_fill_keys($this->checkParamns($uri), null);
    }

    //"/clientes/{cod_cliente}
    //"clientes/55"
    private function checkParamns($uri)
    {
        $exp = "(\{[a-z0-9_]{1,}\})";
        if (preg_match_all($exp, $uri, $m)) {
            return preg_replace('(\{|\})', '', $m[0]);
        }

        return [];
    }


    public function setParamns(array $paramns)
    { //['cod_cliente'=>22]
        foreach ($this->paramns as $key => &$param) {
            if (array_key_exists($key, $paramns)) {
                $param = $paramns[$key];
            }
        }
        return $this;
    }

    public function defineParamns(array $paramns)
    {
        foreach ($this->paramns as &$paramn) {
            $paramn = array_shift($paramns);
        }
        return $this;
    }

    public function name($name)
    {
        Router::swapName($this->name, $name);
        $this->name = $name;
        return $this;
    }



    public function getUrl(array $paramns = [])
    {
        $this->setParamns($paramns);
        $base_url = defined("APPLICATION_URL") ? constant('APPLICATION_URL') : "";
        $base_url = preg_replace("/\/$/", '', $base_url);
        $url = $this->uri;
        foreach ($this->paramns as $key => $value) {
            if (is_null($value)) {
                throw new Exception("$key é um parametro requerido para essa url");
            }
            $url = str_replace("{" . $key . "}", rawurlencode($value), $url);
        }
        return $base_url . $url;
    }

    public function __toString()
    {
        return $this->getUrl();
    }

    public function exec()
    {
        $callback = $this->callback;
        if (is_array($callback) && is_subclass_of($callback[0], Controller::class)) {
            $callback[0] = $this->execController($callback);
            if ($callback[0] === false) {
                return false;
            }
        }
        if (!$this->active) {
            $middlewares = $this->execMiddlewares();
            if ($middlewares !== true) {
                return $middlewares;
            }
            $this->active = true;
        }
        Session::requestRegister();
        return call_user_func_array($callback, $this->paramns);

    }

    private function execController($callback)
    {
        $obj = new $callback[0];
        $obj->middleware($this->middlewares);
        if (!$obj->execMiddlewares()) {
            return false;
        }
        $this->active = true;
        return $obj;
    }

    public function getMethod()
    {
        return $this->method;
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function isActive(){
        return $this->active;
    }

    public function redirect()
    {
        header("Location: $this");
        exit;
    }


}
