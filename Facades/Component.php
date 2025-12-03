<?php

namespace Fmk\Facades;

class Component extends View
{
    protected $voids = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

    protected $tag;

    protected $tab = false;

    protected $content = '';

    protected $attributes = [];

    
}
  