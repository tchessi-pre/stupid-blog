<?php

use App\Class\Controller;
use App\Router\Router;

require_once 'vendor/autoload.php';

$router = new Router($_SERVER['REQUEST_URI']);

$router->setBasePath('/stupid-blog/');

$router->get('/', function () {
    $controller = new Controller();
    $controller->render('index');
}, "home");

$router->get('/login', function () {
    $controller = new Controller();
    $controller->render('login');
}, "login");

$router->post('/login', function () {
    try {
        $controller = new Controller();
        $controller->loginUser($_POST['email'], $_POST['password']);
    } catch (\Exception $e) {
        $controller->render('login', ['error' => $e->getMessage()]);
    }
}, "login");

$router->get('/register', function () {
    try {
        $controller = new Controller();
        $controller->render('register');
    } catch (\Exception $e) {
        $controller->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->post('/register', function () {
    try {
        $controller = new Controller();
        $controller->registerUser($_POST['email'], $_POST['password'], $_POST['password_confirm'], $_POST['firstname'], $_POST['lastname']);
        $controller->redirect('login');
    } catch (\Exception $e) {
        $controller->render('register', ['error' => $e->getMessage()]);
    }
}, "register");
$router->run();
