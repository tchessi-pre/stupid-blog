<?php

namespace App\Class;

use App\Router\Router;

class Controller
{

    public function render($view, $params = [])
    {
        ob_start();
        extract($params);
        require_once 'src/views/' . $view . '.php';
        $content = ob_get_clean();
        require_once 'src/views/partials/header.php';
        echo $content;
        require_once 'src/views/partials/footer.php';
    }

    public function redirect($url, $params = [], $code = 302, $message = null)
    {
        $url = Router::url($url, $params);
        header("Location: $url", true, $code);
        exit();
    }

    public function registerUser($email, $password, $confirmPassword, $firstname, $lastname)
    {
        $user = new User();

        if (empty($email) || empty($password) || empty($confirmPassword) || empty($firstname) || empty($lastname)) {
            throw new \Exception("Tous les champs sont obligatoires");

            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");

            return;
        }

        if ($user->findOneByEmail($email)) {
            throw new \Exception("L'email existe déjà");

            return;
        }

        if ($password === $confirmPassword) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRole(['ROLE_USER']);
            $user->save();

            return;
        } else {
            throw new \Exception("Les mots de passe ne correspondent pas");

            return;
        }
    }

    public function loginUser($email, $password)
    {
        $user = new User();

        if (empty($email) || empty($password)) {
            throw new \Exception("Tous les champs sont obligatoires");
            $this->redirect('login');

            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
            $this->redirect('login');

            return;
        }

        $user = $user->findOneByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            $user->setPassword('');
            $_SESSION['user'] = $user;

            $this->redirect('home');

            return;
        } else {
            throw new \Exception("Les identifiants sont incorects");
            $this->redirect('login');

            return;
        }
    }

    public function logoutUser()
    {
        unset($_SESSION['user']);
        $this->redirect('home');
    }

    public static function getUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            return null;
        }
    }

    public function profile()
    {
        $user = new User();
        if (self::getUser() === null) {
            $this->redirect('login');

            return;
        }
        $user = $user->findOneById($_SESSION['user']->getId());
        if ($user) {
            $user->setPassword('');
            $this->render('profile', ['user' => $user]);

            return;
        }

        $this->redirect('login');

        return;
    }

    public function paginatedPosts($page)
    {
        $post = new Post();
        $posts = $post->findAllPaginated($page);
        $pages = count($post->findAll()) / 10;
        $this->render('posts', ['posts' => $posts, 'pages' => $pages]);
    }

    public function viewPost($id, $error = null)
    {
        if (is_numeric($id) === false) {
            throw new \Exception("L'identifiant du post n'est pas valide");

            return;
        }
        $post = new Post();
        $post = $post->findOneById((int) $id);
        $this->render('post', ['post' => $post, 'error' => $error]);
    }

    public function createComment($content, $post_id)
    {
        if (empty($content)) {
            throw new \Exception("Le contenu ne peut pas être vide");

            return;
        }

        if (self::getUser() === null) {
            throw new \Exception("Vous devez être connecté pour commenter");

            return;
        }

        if (is_numeric($post_id) === false) {
            throw new \Exception("L'identifiant du post n'est pas valide");

            return;
        }

        $post_id = (int) $post_id;

        $post = new Post();
        $post = $post->findOneById($post_id);

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setUser(self::getUser());
        $comment->setPost($post);
        $comment->setCreatedAt(new \DateTime());
        $comment->save();

        $this->redirect('post', ['id' => $post->getId()]);
    }

    public function admin($action = 'list', $entity = 'user', $id = null)
    {
        if (self::getUser() === null || !in_array('ROLE_ADMIN', self::getUser()->getRole())) {
            $this->redirect('home');

            return;
        }

        $action = $action . 'Admin';

        if (method_exists($this, $action)) {
            $this->$action($entity, $id);
        } else {
            throw new \Exception("L'action demandée n'existe pas");
        }
    }

    public function listAdmin($entity)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $entities = $class->findAll();
        $entities = array_map(function ($instance) {
            return $instance->toArray();
        }, $entities);
        $this->render('admin/list', [
            'entities' => $entities,
            'entityName' => $entity
        ]);
    }

    public function showAdmin($entity, $id)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $instance = $class->findOneById($id);
        var_dump($instance->toArray());
        $this->render('admin/show', [
            'entity' => $instance,
            'entityName' => $entity
        ]);
    }
}
