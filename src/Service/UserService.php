<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Model\UserModel;
use App\Model\PostModel;
use App\Model\CommentModel;
use App\Controller\UserController;
use App\Class\Redirector;
use App\View\ViewRenderer;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function addPost(PostModel $post): UserModel
    {
        $user = new UserModel($post->getUserId());
        if (!in_array($post, $user->getPosts()) && $post->getUserId() === $user->getId() && [] !== $user->getPosts()) {
            // $this->posts[] = $post;
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