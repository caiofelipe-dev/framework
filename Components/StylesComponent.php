<?php

namespace Fmk\Components;

class StylesComponent extends ScriptsComponent

{
    protected static $instance;
    protected function __construct(){
        parent::__construct('styles.php');
    }
  
    public static function renderScript($src){
        return "<link rel=\"stylesheet\" href=\"$src\">\n";
    }

    
}