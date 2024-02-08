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

$userController = new UserController($services['userService'](), $services['viewRenderer'](), $services['redirector']());
$postController = new PostController($services['postService'](), $services['viewRenderer'](), $services['redirector'](), $services['postRepository']());
$commentController = new CommentController($services['commentService'](), $services['viewRenderer'](), $services['redirector']());

$router = new Router($_SERVER['REQUEST_URI']);
$router->setBasePath('/stupid-blog/');

$router->get('/', function () use ($services) {
    $services['viewRenderer']()->render('index');
}, "home");

// Authentification 

$router->get('/register', function () use ($services) {
    try {
        $services['viewRenderer']()->render('register');
    } catch (\Exception $e) {
        $services['viewRenderer']()->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->post('/register', function () use ($userController) {
    $userController->registerUser($_POST);
}, "register");

$router->get('/login', function () use ($services) {
    $services['viewRenderer']()->render('login');
}, "login");

$router->post('/login', function () use ($userController) {
    $userController->loginUser($_POST);
}, "login");

$router->get('/logout', function () use ($userController) {
    $userController->logoutUser();
}, "logout");

// Profile

$router->get('/profile', function () use ($userController) {
    $userController->profile();
}, "profile");

$router->post('/profile', function () use ($userController) {
    // var_dump($_POST); die;
    $userController->update($_POST);

}, "profile"); 

// Pagination 

$router->get('/posts/:page', function ($page = 1) use ($postController) {
    $postController->paginatedPosts($page);
}, "posts")->with('page', '[0-9]+');

// Affichage et création des postes 

$router->get('/post/:id', function ($id) use ($postController) {
    $postController->viewPost($id);
}, "post")->with('id', '[0-9]+');

// Créer un commentaire 

$router->post('/comments/:post_id', function ($post_id) use ($commentController, $services) {
    try {
        $commentController->create($_POST);
    } catch (\Exception $e) {
        $services['redirector']()->redirect('post', ['id' => $post_id, 'error' => $e->getMessage()]);
    }
}, "add_comment")->with('post_id', '[0-9]+');

// Gestion administrateur 

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
