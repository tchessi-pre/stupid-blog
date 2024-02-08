<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Model\UserModel;
use App\Model\PostModel;
use App\Model\CommentModel;
use App\Controller\UserController;
use App\Class\Redirector;
use App\Interface\ServiceInterface;
use App\View\ViewRenderer;

class UserService implements ServiceInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    
    public function create($data) {

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $confirmPassword = $data['confirmPassword'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;

        if (empty($email) || empty($password) || empty($confirmPassword) || empty($firstname) || empty($lastname)) {
            throw new \Exception("Tous les champs sont obligatoires");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
        }

        if ($password !== $confirmPassword) {
            throw new \Exception("Les mots de passe ne correspondent pas");
        }

        if ($this->userRepository->findOneByEmail($email)) {
            throw new \Exception("L'email existe déjà");
        }

        $user = new UserModel();
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRole(['ROLE_USER']);

        $this->userRepository->save($user);
    }

    public function update($user) {
        
        
        // if (empty($userId)) {
           
        //     throw new \Exception("ID d'utilisateur invalide");
        // }

        // $user = $this->userRepository->findOneById($userId);
        // if (!$user) {
        //     var_dump('coucou'); die;
        //     throw new \Exception("Utilisateur introuvable");
        // }

        // if (empty($email) || empty($firstname) || empty($lastname)) {
        //     throw new \Exception("Tous les champs sont obligatoires");
        // }

        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     throw new \Exception("L'email n'est pas valide");
        // }
        // $user->setEmail($email);
        // $user->setFirstname($firstname);
        // $user->setLastname($lastname);

        $this->userRepository->save($user);
    }

    public function delete($user) {
        if (empty($userId)) {
            throw new \Exception("ID d'utilisateur invalide");
        }

        $user = $this->userRepository->findOneById($userId);
        if (!$user) {
            throw new \Exception("Utilisateur introuvable");
        }

        $this->userRepository->delete($user->getId());
    }

    public function getById($id): ?UserModel {
        return $this->userRepository->findOneById($id);
    }

    public function getAll()
    {
      return $this->userRepository->findAll();
    }

    public function toArray($user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'role' => $user->getRole()
        ];
    }



    public function updateProfile(UserModel $user)
    {
        $this->userRepository->save($user);
    }
    


    public function hasRole($userId, $role): bool {
        $user = $this->userRepository->findOneById($userId);
        if ($user) {
            return in_array($role, $user->getRole()); 
        }
        return false;
    }

    public function authenticate($data): bool {

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
      
        if (empty($email) || empty($password)) {
            throw new \Exception("Tous les champs sont obligatoires");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
        }

        $user = $this->userRepository->findOneByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new \Exception("Les identifiants sont incorrects");
        }

        $user->setPassword('');
        $_SESSION['user'] = $user;

        return true;
    }

    public function register($data)
    {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $confirmPassword = $data['password_confirm'] ?? ''; // Assurez-vous que le nom de clé correspond à celui du formulaire HTML
        $firstname = $data['firstname'] ?? '';
        $lastname = $data['lastname'] ?? '';

        if (empty($email) || empty($password) || empty($confirmPassword) || empty($firstname) || empty($lastname)) {
            throw new \Exception("Tous les champs sont obligatoires");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("L'email n'est pas valide");
        }

        if ($this->userRepository->findOneByEmail($email)) {
            throw new \Exception("L'email existe déjà");
        }

        if ($password !== $confirmPassword) {
            throw new \Exception("Les mots de passe ne correspondent pas");
        }
        $user = new UserModel();
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRole(['ROLE_USER']);
        $this->userRepository->save($user);
    }

    public function addPost(PostModel $post): UserModel
    {
        $user = new UserModel($post->getUserId());
        if (!in_array($post, $user->getPosts()) && $post->getUserId() === $user->getId() && [] !== $user->getPosts()) {
            $user->setPosts($post[]);
        } else {
            $user->getPosts();
            $user->setPosts($post[]);
        }

        return $user;
    }


    public function removePost(PostModel $post): UserModel
    {
        $user = new UserModel($post->getUserId());
        $key = array_search($post, $user->getPosts());
        if ($key !== false) {
            unset($user->getPosts()[$key]);
        }

        return $user;
    }


        /**
     * addRole
     *
     * @param string $role
     * @return self
     */
    public function addRole(string $role): UserModel
    {
        $user = new UserModel();
        $user->getRole()[] = $role;

        return $user;
    }

    /**
     * removeRole
     *
     * @param string $role
     * @return self
     */
    public function removeRole(string $role): UserModel
    {
        $user = new UserModel();
        $key = array_search($role, $user->getRole());
        if ($key !== false) {
            unset($user->getRole()[$key]);
        }

        return $user;
    }

    public function addComment(CommentModel $comment): UserModel
    {
        $user = new UserModel($comment->getUserId());
        $user->getComments()[] = $comment;

        return $user;
    }

    public function removeComment(CommentModel $comment): UserModel
    {
        $user = new UserModel($comment->getUserId());
        $key = array_search($comment, $user->getComments()[]);
        if ($key !== false) {
            unset($this->$user->getComments()[$key]);
        }

        return $user;
    }

    public function admin($action = 'list', $entity = 'user', $id = null)
    {
        if (UserController::getUser() === null || !in_array('ROLE_ADMIN', UserController::getUser()->getRole())) {
            $redirector = new Redirector;
            $redirector->redirect('home');

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
        $viewRenderer = new ViewRenderer;
        $viewRenderer->render('admin/list', [
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
        $viewRenderer = new ViewRenderer;
        $viewRenderer->render('admin/show', [
            'entity' => $instance,
            'entityName' => $entity
        ]);
    }

    public function editAdmin($entity, $id)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $instance = $class->findOneById($id);
        foreach ($_POST as $key => $value) {
            $key = explode('_', $key);
            $key = array_map(function ($word) {
                return ucfirst($word);
            }, $key);
            $key = implode('', $key);
            $value = htmlspecialchars($value);
            $getter = 'get' . $key;
            if (method_exists($instance, $getter) && is_array($instance->$getter())) {
                $value = explode(', ', $value);
            }
            if (is_string($value) && strtotime($value)) {
                $value = (new \DateTime())->setTimestamp(strtotime($value));
            }
            if ($key === 'User' || $key === 'Post' || $key === 'Comments' || $key === 'Category') {
                continue;
            }
            $setter = 'set' . $key;
            if (method_exists($instance, $setter) && null !== $instance->$getter()) {
                $instance->$setter($value);
            }
        }

        $instance->save();
        $redirector = new Redirector;
        $redirector->redirect('admin', ['entity' => strtolower($entity), 'action' => 'list']);
    }

    public function deleteAdmin($entity, $id)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $instance = $class->findOneById($id);
        $instance->delete();
        $redirector = new Redirector;

        if ($entity === 'User' && $id === UserController::getUser()->getId()) {
            $redirector->redirect('logout');
        }

        $redirector->redirect('admin', ['entity' => strtolower($entity), 'action' => 'list']);
    }

    
}