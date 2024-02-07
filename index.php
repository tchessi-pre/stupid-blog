<?php
use App\Class\Database;
use App\Controller\CommentController;
use App\Controller\PostController;
use App\Class\Redirector;
use App\Controller\UserController;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Router\Router;
use App\Service\CommentService;
use App\Service\PostService;
use App\Service\UserService;
use App\View\ViewRenderer;
require_once 'vendor/autoload.php';

session_start();

$services = [];
$services['db'] = function () {
    return new Database();
};
$services['viewRenderer'] = function () {
    return new ViewRenderer();
};
$services['redirector'] = function () {
    return new Redirector();
};
$services['userRepository'] = function () use ($services) {
    return new UserRepository($services['db']()->getConnection());
};

$services['postRepository'] = function () use ($services) {
    return new PostRepository($services['db']()->getConnection());
};

$services['commentRepository'] = function () use ($services) {
    return new CommentRepository($services['db']()->getConnection());
};

$services['userService'] = function () use ($services) {
    return new UserService($services['userRepository']());
};

$services['postService'] = function () use ($services) {
    return new PostService($services['postRepository']());
};

$services['commentService'] = function () use ($services) {
    return new CommentService($services['commentRepository']());
};


$router = new Router($_SERVER['REQUEST_URI']);

$router->setBasePath('/stupid-blog/');

$router->get('/', function () use ($services) {
    $services['viewRenderer']()->render('index');
}, "home");

$router->get('/register', function () use ($services) {
    try {
        $services['viewRenderer']()->render('register');
    } catch (\Exception $e) {
        $services['viewRenderer']()->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->post('/register', function () use ($services) {
    $userService = new UserService($services['userRepository']());
    $userController = new UserController($userService, $services['viewRenderer'](), $services['redirector']());
    $userController->registerUser($_POST);
}, "register");

$router->get('/login', function () use ($services) {
    $services['viewRenderer']()->render('login');
}, "login");

$router->post('/login', function () use ($services) {
    $userController = new UserController($services['userService'](), $services['viewRenderer'](), $services['redirector']());
    $userController->loginUser($_POST);
}, "login");

$router->get('/logout', function () use ($services) {
    $userController = new UserController($services['userService'](), $services['viewRenderer'](), $services['redirector']());
    $userController->logoutUser();
}, "logout");

$router->get('/profile', function () use ($services) {
    $userController = new UserController($services['userService'](), $services['viewRenderer'](), $services['redirector']());
    $userController->profile();
}, "profile");

$router->post('/profile', function () use ($services) {
    $userController = new UserController($services['userService'](), $services['viewRenderer'](), $services['redirector']());
    $userId = $_SESSION['user']->getId();
    $userController->update($_POST);

}, "profile"); 

$router->get('/posts/:page', function ($page = 1) use ($services) {
    $postController = new PostController($services['postService'](), $services['viewRenderer'](), $services['redirector'](), $services['postRepository']());
    $postController->paginatedPosts($page);
}, "posts")->with('page', '[0-9]+');

$router->get('/post/:id', function ($id) use ($services) {
    $postController = new PostController($services['postService'](), $services['viewRenderer'](), $services['redirector'](), $services['postRepository']());
    $postController->viewPost($id);
}, "post")->with('id', '[0-9]+');

$router->post('/comments/:post_id', function ($post_id) use ($services) {
    $commentController = new CommentController($services['commentService'](), $services['viewRenderer'](), $services['redirector']());
    try {
        $commentController->create(['post_id' => $post_id, 'content' => $_POST['content']]);
    } catch (\Exception $e) {
        $services['redirector']()->redirect('post', ['id' => $post_id, 'error' => $e->getMessage()]);
    }
}, "add_comment")->with('post_id', '[0-9]+');

$router->get('/admin/:action/:entity', function ($action = 'list', $entity = 'user') use ($services) {
    $services['userService']()->admin($action, $entity);
}, "admin")->with('action', 'list')->with('entity', 'user|post|comment|category');

$router->get('/admin/:action/:entity/:id', function ($action, $entity, $id = null) use ($services) {
    $services['userService']()->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'show|edit|delete')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');

$router->post('/admin/:action/:entity/:id', function ($action, $entity, $id = null) use ($services) {
    $services['userService']()->admin($action, $entity, $id);
}, "admin-entity-action")->with('action', 'edit|delete')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');


$router->run();
