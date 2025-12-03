<?php

namespace Fmk\Components;

class StylesComponent extends ScriptsComponent

{
    protected static $instance;
    protected function __construct(){
        parent::__construct('styles.php');
    }
  
    public static function renderScript($src){
        if(!str_contains($src, 'https://')) {
            $src = defined('APPLICATION_URL') ?
                constant('APPLICATION_URL').DIRECTORY_SEPARATOR.$src : constant('APPLICATION_PATH').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$src;
        }
        return "<link rel=\"stylesheet\" href=\"$src\">\n";
    }

    
}