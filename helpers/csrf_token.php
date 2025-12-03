<?php

if(!function_exists('csrf_token')){
    function csrf_token(){
        $token =  \Fmk\Facades\CSRF::token();
        $token_name = \Fmk\Facades\CSRF::TOKEN_NAME;
        return "<input type='hidden' name='$token_name' value='$token'>";
    }
}