<?php

namespace Fmk\Facades;

use Fmk\Initialize;
use Fmk\Interfaces\Rule;

class Validate{
    protected array $errors = [];

    protected $value;

    protected $name;

    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }

    public function __call($method, $args){
        $rules = require Initialize::$configs_path."rules.php";
        try{
            $local_rules = Config::get('rules', []);
        }catch(\Exception $e){
            $local_rules = [];
        }
        $rules = array_merge($rules, $local_rules);
        if(isset($rules[$method])){
            $ruleClass = $rules[$method];
            $reflection = new \ReflectionClass($ruleClass);
            $rule = $reflection->newInstanceArgs($args);
            return $this->validate($rule);
        }
        throw new \Exception("Regra de validação $method não encontrada.");
    }

    public function addError($message){
        $this->errors[] = $message;
        return $this;
    }

    public function getErrors(){
        return $this->errors;
    }
    public function getValue(){
        return $this->value;
    }

    public function __toString(){
        return (string) $this->getValue();

    }

    public function check(){
        return empty($this->errors);
    }

    public function validate(Rule $rule){
        if(!$rule->passes($this->value)){
            $this->addError($rule->error($this->name));
        }
        return $this;
    }

}