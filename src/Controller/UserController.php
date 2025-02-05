<?php

namespace App\Controller;

use App\Service\UserService;
use App\View\ViewRenderer;
use App\Class\Redirector;
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

    public function create($request) {
        try {
            $email = $request['email'] ?? '';
            $password = $request['password'] ?? '';
            $confirmPassword = $request['confirm_password'] ?? '';
            $firstname = $request['firstname'] ?? '';
            $lastname = $request['lastname'] ?? '';

            $this->userService->create($email, $password, $confirmPassword, $firstname, $lastname);

            $this->redirector->redirect('login');
        } catch (\Exception $e) {
            $this->redirector->redirect('register', ['error' => $e->getMessage()]);
        }
    }


    public function update($request) {
        try {
            $this->userService->update($request);

            $this->redirector->redirect('profile', ['success' => 'Profil mis à jour avec succès']);
        } catch (\Exception $e) {
            $this->redirector->redirect('profile', ['error' => $e->getMessage()]);
        }
    }

    public function delete($request) {
        try {
            $userId = $request['user_id'] ?? null;
            $this->userService->delete($userId);
            $this->redirector->redirect('home', ['success' => 'Utilisateur supprimé avec succès']);
        } catch (\Exception $e) {
            $this->redirector->redirect('profile', ['error' => $e->getMessage()]);
        }
    }


    // Authentification 

    public function registerUser($data)
    {
        try {
            $this->userService->register($data);
            $this->redirector->redirect('login');
        } catch (\Exception $e) {
            $this->redirector->redirect('register', ['error' => $e->getMessage()]);
        }
    }

    public function loginUser($data) {
        try {
            $this->userService->authenticate($data);
            $this->redirector->redirect('home');
        } catch (\Exception $e) {
            $this->redirector->redirect('login', ['error' => $e->getMessage()]);
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

    public function profile() {
        if (self::getUser() === null) { 
            $this->redirector->redirect('login');
            return;
        }

        $userId = $_SESSION['user']->getId(); 
        $user = $this->userService->getById($userId);
        
        if ($user) {
            $this->viewRenderer->render('profile', ['user' => $user]);
        } else {
            $this->redirector->redirect('login');
        }
    }

}
