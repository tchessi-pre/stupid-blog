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


$router = new Router($_SERVER['REQUEST_URI']);

$router->setBasePath('/stupid-blog/');

$router->get('/', function () {
    $viewRenderer = new ViewRenderer;
    $viewRenderer->render('index');
}, "home");

$router->get('/register', function () {
    try {
        $viewRenderer = new ViewRenderer;
        $viewRenderer->render('register');
    } catch (\Exception $e) {
        $viewRenderer->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->post('/register', function () {
    try {
        $db = new Database();
        $connection = $db->getConnection();
        $userRepository = new UserRepository($connection);
        $userService = new UserService($userRepository);
        $viewRenderer = new ViewRenderer;
        $redirector = new Redirector;
        $userController = new UserController($userService, $viewRenderer, $redirector);
        $userController->registerUser($_POST['email'], $_POST['password'], $_POST['password_confirm'], $_POST['firstname'], $_POST['lastname']);
        $redirector->redirect('login');
    } catch (\Exception $e) {
        $viewRenderer->render('register', ['error' => $e->getMessage()]);
    }
}, "register");


$router->get('/login', function () {
    $viewRenderer = new ViewRenderer;
    $viewRenderer->render('login');
}, "login");

$router->post('/login', function () {
    try {

        $db = new Database();
        $connection = $db->getConnection();
        $userRepository = new UserRepository($connection);
        $userService = new UserService($userRepository);
        $viewRenderer = new ViewRenderer;
        $redirector = new Redirector;
        $userController = new UserController($userService, $viewRenderer, $redirector);
        $userController->loginUser($_POST['email'], $_POST['password']);
    } catch (\Exception $e) {
        $viewRenderer->render('login', ['error' => $e->getMessage()]);
    }
},
    "login"
);

$router->get('/logout', function () {
    $db = new Database();
    $connection = $db->getConnection();
    $userRepository = new UserRepository($connection);
    $userService = new UserService($userRepository);
    $viewRenderer = new ViewRenderer;
    $redirector = new Redirector;
    $userController = new UserController($userService, $viewRenderer, $redirector);
    $userController->logoutUser();
}, "logout");


$router->get('/profile', function () {
    $db = new Database();
    $connection = $db->getConnection();
    $userRepository = new UserRepository($connection);
    $userService = new UserService($userRepository);
    $viewRenderer = new ViewRenderer;
    $redirector = new Redirector;
    $userController = new UserController($userService, $viewRenderer, $redirector);
    $userController->profile();
}, "profile");


$router->get('/posts/:page', function ($page = 1) {
    $db = new Database();
    $connection = $db->getConnection();
    $postRepository = new PostRepository($connection);
    $postService = new PostService($postRepository);
    $viewRenderer = new ViewRenderer;
    $redirector = new Redirector;
    $controller = new PostController($postService, $viewRenderer, $redirector);
    $controller->paginatedPosts($page);
}, "posts")->with('page', '[0-9]+');


$router->get('/post/:id', function ($id) {
    $db = new Database();
    $connection = $db->getConnection();
    $postRepository = new PostRepository($connection);
    $postService = new PostService($postRepository);
    $viewRenderer = new ViewRenderer;
    $redirector = new Redirector;
    $controller = new PostController($postService, $viewRenderer, $redirector);
    $controller->viewPost($id);
}, "post")->with('id', '[0-9]+');



$router->post('/comments/:post_id', function ($post_id) {
    $db = new PDO('mysql:host=localhost;dbname=solid-blog;charset=utf8', 'root', '', [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_TIMEOUT => 90
    ]); 
    $commentRepository = new CommentRepository($db);
    $commentService = new CommentService($commentRepository);
    $viewRenderer = new ViewRenderer; 
    $redirector = new Redirector;
    $commentController = new CommentController($commentService, $viewRenderer, $redirector);

    try {
        $commentController->create(['post_id' => $post_id, 'content' => $_POST['content']]);
    } catch (\Exception $e) {
        $redirector->redirect('post', ['id' => $post_id, 'error' => $e->getMessage()]);
    }
}, "add_comment")->with('post_id', '[0-9]+');






$router->get('/admin/:action/:entity', function ($action = 'list', $entity = 'user') {
    $db = new Database();
    $connection = $db->getConnection();
    $userRepository = new UserRepository($connection);
    $userService = new UserService($userRepository);
    $userService->admin($action, $entity);
}, "admin")->with('action', 'list')->with('entity', 'user|post|comment|category');

$router->get('/admin/:action/:entity/:id', function ($action = 'list', $entity = 'user', $id = null) {
    $db = new Database();
    $connection = $db->getConnection();
    $userRepository = new UserRepository($connection);
    $userService = new UserService($userRepository);
    $userService->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'show')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');

$router->post('/admin/:action/:entity/:id', function ($action = 'list', $entity = 'user', $id = null) {
    $db = new Database();
    $connection = $db->getConnection();
    $userRepository = new UserRepository($connection);
    $userService = new UserService($userRepository);
    $userService->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'edit|delete')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');


$router->run();
