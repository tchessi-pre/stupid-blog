<?php

use App\Router\Router;

require_once 'vendor/autoload.php';

$router = new Router($_SERVER['REQUEST_URI']);

$router->setBasePath('/stupid-blog/');

$router->get('/', function () {
    echo 'Home';
});

$router->get('/posts', function () {
    echo 'All posts';
});

$router->run();
