<?php

namespace Fmk\Facades;

use Fmk\Traits\Singleton;
use Fmk\Traits\Validate;

class Request
{
    use Singleton;
    use Validate;

    protected $uri;
    protected $method;
    protected $data;


    const REQUEST_KEY = 'request_uri';
    final protected function __construct()
    {
        Session::getInstance();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($this->method === 'GET') {
            $this->data = $_GET ?? [];
        } else {
            $this->data = $_REQUEST ?? [];
        }
        $this->uri = $this->data[static::REQUEST_KEY] ?? '';
        unset($this->data[static::REQUEST_KEY]);
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    protected function all()
    {
        return $this->data;
    }

    protected function getUri()
    {
        return $this->uri;
    }
    protected function getMethod()
    {
        return $this->method;
    }


    protected function toArray()
    {
        $uri = $this->uri;
        $data = $this->data;
        $method = $this->method;
        return compact('data', 'uri', 'method');
    }

    protected function exec()
    {
        $route = $this->getRoute();
        if ($route) {
            return $route->exec();
        }
        return Router::error404();
    }

    protected function getRoute()
    {
        return Router::getRouteByUri($this->uri, $this->method);
    }

    protected function back()
    {
        $old = Session::request_old();
        if ($old) {
            $route = Router::getRouteByUri($old['uri'] , $old['method']);
            if ($route)
                return $route->redirect();
        }
        header("Location: /");
        exit;
    }

    protected function only($names){
        $names = is_array($names)?$names:func_get_args();
        $data = [];
        foreach($names as $key){
            $data[$key] = $this->$key;
        }
        return $data;
    }



}
