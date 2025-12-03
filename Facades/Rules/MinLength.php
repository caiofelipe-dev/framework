<?php

namespace Fmk\Facades\Rules;

class MinLength implements \Fmk\Interfaces\Rule
{
    protected int $length;

    public function __construct(int $length)
    {
        $this->length = $length;
    }

    public function passes($value): bool
    {
        return strlen($value) >= $this->length;
    }

    public function error($atribute): string
    {
        return "O campo $atribute deve ter no mínimo {$this->length} caracteres.";
    }
}