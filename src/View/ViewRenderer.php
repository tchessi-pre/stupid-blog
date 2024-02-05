<?php

namespace App\View;

class ViewRenderer
{
    public function render($view, $data = [])
    {
        extract($data);
        require "views/{$view}.php";
    }
}
