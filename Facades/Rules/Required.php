<?php

namespace Fmk\Facades\Rules;

class Required implements \Fmk\Interfaces\Rule
{
    public function passes($value): bool
    {
        return isset($value) && !empty(trim($value));
    }

    public function error($atribute): string
    {
        return "O campo $atribute é de preesnchimento obrigatório.";
    }
}