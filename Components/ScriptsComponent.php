<?php

namespace Fmk\Components;

use Fmk\Facades\Component;

class ScriptsComponent extends Component
{
    protected $scripts = [];
    protected static $instance;
    protected function __construct($file_name = 'scripts.php'){
        $scripts_file = constant('CONFIG_PATH') . DIRECTORY_SEPARATOR . $file_name;
        foreach(require $scripts_file as $key => $src){
            $this->scripts[$key] = ['use' => false, 'script' => $src];
        }
    }
    public static function addScript($scripts){
        $scripts = (is_array($scripts)) ? $scripts : func_get_args();
        $instance = static::getInstance();
        foreach($scripts as $key){
            if(!isset($instance->scripts[$key])){
                throw new \Exception("Script $key not found");
            }
            $instance->scripts[$key]['use'] = true;
        }
        return $instance;
    }

    protected static function getInstance(){
        if(!isset(static::$instance)){
                static::$instance = new static();
        }
        return static::$instance;
    }

    public static function show($scripts = []){
        $scripts = (is_array($scripts)) ? $scripts : func_get_args();
        if(!empty($scripts)){
            static::addScript($scripts);
        }
        $scripts = static::getInstance()->scripts;
        ob_start();
        foreach($scripts as $script){
            if($script['use']){
                echo static::renderScript($script['script']);
            }
        }
        return ob_get_clean();
    }

    protected static function renderScript($src){
        if(!str_contains($src, 'https://')) {
            $src = defined('APPLICATION_URL') ?
                constant('APPLICATION_URL').DIRECTORY_SEPARATOR.$src : constant('APPLICATION_PATH').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$src;
        }
        return "<script src=\"$src\"></script>\n";
    }



    
}