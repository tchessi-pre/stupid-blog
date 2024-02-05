<?php

namespace App\Class;
use App\Router\Router;

class Redirector
{
    public function redirect($url, $params = [], $code = 302, $message = null)
    {
        $url = Router::url($url, $params);
        header("Location: $url", true, $code);
        exit();
    }
}
