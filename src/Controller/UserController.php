<?php

namespace App\Controller;

use App\Class\Database;
use App\Service\UserService;
use App\Class\Redirector;
use App\Model\UserModel;
use App\Repository\UserRepository;
use App\View\ViewRenderer;
use App\Interface\ControllerInterface;

class UserController implements ControllerInterface
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
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
        }

        if ($userRepository->findOneByEmail($email)) {
            throw new \Exception("L'email existe déjà");
        }

        if ($password === $confirmPassword) {
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRole(['ROLE_USER']);
            $userRepository->save($user);
        } else {
            throw new \Exception("Les mots de passe ne correspondent pas");
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
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
            $this->redirector->redirect('login');
        }

        $user = $userRepository->findOneByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            $user->setPassword('');
            $_SESSION['user'] = $user;
            $this->redirector->redirect('home');
        } else {
            throw new \Exception("Les identifiants sont incorrects");
            $this->redirector->redirect('login');
        }
    }

    public function logoutUser()
    {
        unset($_SESSION['user']);
        $this->redirector->redirect('home');
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
        $db = new Database();
        $connection = $db->getConnection();
        $user = new UserModel();
        $userRepository = new UserRepository($connection);
        if (self::getUser() === null) {
            $this->redirector->redirect('login');
        }
        $user = $userRepository->findOneById($_SESSION['user']->getId());
        if ($user) {
            $user->setPassword('');
            $this->viewRenderer->render('profile', ['user' => $user]);
        } else {
            $this->redirector->redirect('login');
        }
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

    public function create($request)
    {
        try {
            $email = $request['email'] ?? '';
            $password = $request['password'] ?? '';
            $confirmPassword = $request['confirm_password'] ?? '';
            $firstname = $request['firstname'] ?? '';
            $lastname = $request['lastname'] ?? '';

            if (empty($email) || empty($password) || empty($confirmPassword) || empty($firstname) || empty($lastname)) {
                throw new \Exception("Tous les champs sont obligatoires");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("L'email n'est pas valide");
            }

            if ($password !== $confirmPassword) {
                throw new \Exception("Les mots de passe ne correspondent pas");
            }

            $db = new Database();
            $connection = $db->getConnection();
            $userRepository = new UserRepository($connection);

            if ($userRepository->findOneByEmail($email)) {
                throw new \Exception("L'email existe déjà");
            }

            $user = new UserModel();
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRole(['ROLE_USER']);

            $userRepository->save($user);

            $this->redirector->redirect('login');
        } catch (\Exception $e) {
            $this->redirector->redirect('register', ['error' => $e->getMessage()]);
        }
    }


    public function update($request)
    {
        try {
            $userId = $request['user_id'] ?? null;
            $email = $request['email'] ?? '';
            $password = $request['password'] ?? '';
            $confirmPassword = $request['confirm_password'] ?? '';
            $firstname = $request['firstname'] ?? '';
            $lastname = $request['lastname'] ?? '';

            if (empty($userId)) {
                throw new \Exception("ID d'utilisateur invalide");
            }

            if (empty($email) || empty($firstname) || empty($lastname)) {
                throw new \Exception("Tous les champs sont obligatoires");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("L'email n'est pas valide");
            }

            $db = new Database();
            $connection = $db->getConnection();
            $userRepository = new UserRepository($connection);
            $user = $userRepository->findOneById($userId);
            if (!$user) {
                throw new \Exception("Utilisateur introuvable");
            }

            $user->setEmail($email);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);

            if (!empty($password)) {
                if ($password !== $confirmPassword) {
                    throw new \Exception("Les mots de passe ne correspondent pas");
                }
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            }
            $userRepository->save($user);
            $this->redirector->redirect('profile', ['success' => 'Profil mis à jour avec succès']);
        } catch (\Exception $e) {
            $this->redirector->redirect('profile', ['error' => $e->getMessage()]);
        }
    }


    public function delete($request)
    {
        try {
            $userId = $request['user_id'] ?? null;

            if (empty($userId)) {
                throw new \Exception("ID d'utilisateur invalide");
            }
            $db = new Database();
            $connection = $db->getConnection();
            $userRepository = new UserRepository($connection);
            $user = $userRepository->findOneById($userId);
            if (!$user) {
                throw new \Exception("Utilisateur introuvable");
            }
            $userRepository->delete($userId);
            $this->redirector->redirect('home', ['success' => 'Utilisateur supprimé avec succès']);
        } catch (\Exception $e) {
            $this->redirector->redirect('profile', ['error' => $e->getMessage()]);
        }
    }
}
