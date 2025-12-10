<?php

namespace Fmk\Facades;

class Component extends View
{
    // Tags HTML que não necessitam de fechamento
    protected $voids = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

    protected $tag;

    protected $tab = false;
    protected $depth = 0;

    protected $content = [];

    protected $attributes = [];

    protected $nick_name;

    public function __construct(?string $view_file = NULL)
    {  
        if(isset($view_file))
            return parent::__construct($view_file);
    }
    
    public function addContent($content) {
        if(is_array($content)) {
            foreach($content as $c)
                $this->addContent($c);
        }
        if($content instanceof Component)
            $content->depth++;

        $this->content[] = $content;
        return $this;
    }

    public function tag(string $tag) {
        $this->tag = $tag;
        return $this;
    }

    public function attr(string $atributo, string|array $valor) {
        if($valor == NULL || str_replace(' ', '', $valor) == '' || $valor == [])
            return $this;
            if(\is_array($valor)) {
                foreach($valor as $v) {
                    $this->attr($atributo, $v);
                }
                return $this;
            }
        if(isset($this->attributes["$atributo"]) && \in_array($valor, $this->attributes["$atributo"]))
            return $this;
        $this->attributes["$atributo"][] = trim($valor);
        return $this;
    }
    public function attrs(array $atributos) {
        foreach($atributos as $key => $value) {
            $this->attr($key, $value);
        }
        return $this;
    }
    // 'nav', 'collappse'
    public function class(string ...$classes) {
        $this->attr('class', $classes);
        return $this;
    }
    public function id(string ...$id) {
        $this->attr('id', $id);
        return $this;
    }
    public function name(string $name) {
        $this->attr('name', $name);
        return $this;
    }

    public function updateContent($content) {
        $this->content = is_array($content) ? $content : func_get_args();
        return $this;
    }
    public function updateAttr(string $atributo, string|array $novo_valor) {
        (isset($this->attributes["$atributo"])) || $this->attributes["$atributo"] = $novo_valor;
        return $this;
    }

    public function getContents() {
        $html = '';
        foreach($this->content as $content) {
            if($content instanceof Component) {
                $html .= $content->render();
                continue;
            }
            if(is_array($content)) {
                $html .= implode($content);
            }
            if(is_string($content))
                $html .= $content;
        }
        return $html;
    }

    // component->setTab(false)->render()
    public function setTab(bool $tab = true)
    {
        $this->tab = $tab;
        return $this;
    }

    public function __toString()
    {
        return parent::__toString();
    }
    
    public function render(array $data = [])
    {
        if($this->view_file)
            return parent::render($data);
        
        ob_start();
        extract(array_merge($this->data,$data));
        $tab = $new_line = '';
        if($this->tab) {
            $tab = str_repeat('\t', $this->depth);
            $new_line = '\n';
        }
        echo "$new_line$tab";
        echo ($this->tag) ? $this->renderTag() : $this->getContents();

        return ob_get_clean();
    }

    public function renderTag() {
        $html = '';
        $html .= "<".$this->tag.$this->renderAttrs().">".$this->getContents();
        if(!in_array($this->tag, $this->voids))
            $html .= "</$this->tag>";
        return $html;
    }

    /**
     * Renderiza os atributos
     * id='meu-id' name='meu-name' type='text'
     * @return string
     */
    public function renderAttrs() {
        $html = '';
        foreach($this->attributes as $key => $value) {
            $html .= " $key=\"";
            if(is_array($value)) {
                foreach($value as &$v) {
                    if(is_array($v))
                        $v = implode(' ',$v);
                }
                $html .= implode(' ',$value)."\"";
                continue;
            }
            $html .= "$value\"";
        }
        return $html;
    }
}
  