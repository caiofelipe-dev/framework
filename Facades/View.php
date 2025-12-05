<?php


namespace Fmk\Facades;

class View{
    protected $view_file;
    protected $data = [];
    public function __construct($view_file){
        $this->view_file = $view_file;   
    }

    public function __set($name, $value){
        $this->data[$name] = $value;
    }
    //new View('home'); $view->user = 'John'; $view->setData([]);
    public function setData(array $data){
        $this->data = $data;
        return $this;
    }

    public function __get($name){
        return $this->data[$name] ?? NULL;
    }

    public function render(array $data = []){
        ob_start();
        extract(array_merge($this->data,$data));
        include $this->view_file;
        $html = ob_get_clean();

        // Injetar token CSRF automaticamente em forms POST que não contenham o input de token
        try {
            $tokenName = \Fmk\Facades\CSRF::TOKEN_NAME;
            $tokenValue = \Fmk\Facades\CSRF::token();

            $html = preg_replace_callback('#(<form\b([^>]*)>)(.*?)</form>#is', function($matches) use ($tokenName, $tokenValue){
                $openTag = $matches[1];
                $attrs = $matches[2];
                $inner = $matches[3];

                // Verifica se o form declara method="post"
                if (!preg_match('/method\s*=\s*["\']?post["\']?/i', $attrs)) {
                    return $matches[0];
                }

                // Se o token já existir dentro do form, não duplicar
                if (preg_match('/name\s*=\s*["\']?'.preg_quote($tokenName,'/').'["\']?/i', $inner)) {
                    return $matches[0];
                }

                $input = "<input type=\"hidden\" name=\"$tokenName\" value=\"$tokenValue\">";

                // Insere o input CSRF logo após a tag de abertura do form
                return $openTag . $input . $inner . "</form>";
            }, $html);
        } catch (\Throwable $e) {
            // Em caso de erro na injeção, apenas retorna o HTML sem alterações
        }

        return $html;
    }

    public function __toString(){
        return $this->render();
    }
}