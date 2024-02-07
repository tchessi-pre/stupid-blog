<?php

namespace App\Controller;

use App\Class\Database;
use App\Service\UserService;
use App\Class\Redirector;
use App\Model\UserModel;
use App\Repository\UserRepository;
use App\View\ViewRenderer;


class UserController
{
    protected $userService;
    protected $viewRenderer;
    protected $redirector;

    public function __construct(UserService $userService, ViewRenderer $viewRenderer, Redirector $redirector)
    {
        $this->userService = $userService;
        $this->viewRenderer = $viewRenderer;
        $this->redirector = $redirector;
    }
    public function registerUser($email, $password, $confirmPassword, $firstname, $lastname)
    {
        $db = new Database();
        $connection = $db->getConnection();
        $user = new UserModel();
        $userRepository = new UserRepository($connection);

        if (empty($email) || empty($password) || empty($confirmPassword) || empty($firstname) || empty($lastname)) {
            throw new \Exception("Tous les champs sont obligatoires");

            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");

            return;
        }

        if ($userRepository->findOneByEmail($email)) {
            throw new \Exception("L'email existe déjà");

            return;
        }

        if ($password === $confirmPassword) {
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRole(['ROLE_USER']);
            $userRepository->save($user);

            return;
        } else {
            throw new \Exception("Les mots de passe ne correspondent pas");

            return;
        }
    }

    public function loginUser($email, $password)
    {
        $db = new Database();
        $connection = $db->getConnection();
        $user = new UserModel();
        $userRepository = new UserRepository($connection);

        if (empty($email) || empty($password)) {
            throw new \Exception("Tous les champs sont obligatoires");
            $this->redirector->redirect('login');

            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
            $this->redirector->redirect('login');

            return;
        }

        $user = $userRepository->findOneByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            $user->setPassword('');
            $_SESSION['user'] = $user;

            $this->redirector->redirect('home');

            return;
        } else {
            throw new \Exception("Les identifiants sont incorects");
            $this->redirector->redirect('login');

            return;
        }
    }

    public function logoutUser()
    {
        unset($_SESSION['user']);
        $this->redirector->redirect('home');
    }

    public static function getUser()
    {
        // var_dump($_SESSION['user']); die;
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            return null;
        }
        
    }

    public function profile()
    {
        $db = new Database();
        $connection = $db->getConnection();
        $user = new UserModel();
        $userRepository = new UserRepository($connection);        
        if (self::getUser() === null) {
            $this->redirector->redirect('login');

            return;
        }
        $user = $userRepository->findOneById($_SESSION['user']->getId());
        if ($user) {
            $user->setPassword('');
            $this->viewRenderer->render('profile', ['user' => $user]);

            return;
        }

        $this->redirector->redirect('login');

        return;
    }

    public static function hasRole(string $role): bool
    {
        $db = new Database();
        $connection = $db->getConnection();
        $user = new UserModel();
        $userRepository = new UserRepository($connection); 
        $user = $userRepository->findOneById($_SESSION['user']->getId());

        return in_array($role, $user->getRole());
    }
}