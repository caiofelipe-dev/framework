<?php

namespace Fmk\Traits;

trait Validate{
    protected $validate = [];

    public function validate($name, string|null $label = null){
        $label  = (empty($label)) ? $name : $label;
        return $this->validate[$name] = new \Fmk\Facades\Validate($label, $this->$name);
        //observacoes Observações
        //$request->observacoes
    }

    public function validation(){
        $validation = true;
        foreach($this->validate as $field){
            if(!$field->check()){
                $validation = false;
            }
        }
        return $validation;
    }

    public function getValidate($name){
        return $this->validate[$name] ?? null;
    }
}