<?php

namespace App\Repository;

use PDO;
use App\Model\UserModel;
use App\Interface\RepositoryInterface;

class UserRepository implements RepositoryInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save($user)
    {
        if (!$_SESSION['user']->getId()) {
            $this->insert($user);
        } else {
            // var_dump('couocu'); die;
            $this->update($user);
        }
    }

    public function insert($user)
    {
        $stmt = $this->db->prepare('INSERT INTO user (email, password, firstname, lastname, role) VALUES (:email, :password, :firstname, :lastname, :role)');
        $stmt->bindValue(':email', $user->getEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), \PDO::PARAM_STR);
        $stmt->bindValue(':firstname', $user->getFirstname(), \PDO::PARAM_STR);
        $stmt->bindValue(':lastname', $user->getLastname(), \PDO::PARAM_STR);
        $stmt->bindValue(':role', json_encode($user->getRole()), \PDO::PARAM_STR);
        $stmt->execute();
        $user->setId($this->db->lastInsertId());
    }

    public function update($user)
    {
        $stmt = $this->db->prepare('UPDATE user SET email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id');

        $stmt->execute([
            'id' => $_SESSION['user']->getId(),
            'email' => $user['email'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
        ]);
        
         $_SESSION["user"]->setEmail($user['email']);
         $_SESSION["user"]->setFirstName($user['firstname']);
         $_SESSION["user"]->setLastName($user['lastname']);
        
    }

    public function findOneById(int $userId): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE id = :id');
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$userData) {
            return null;
        }

        $user = new UserModel();
        $user->setId($userData['id']);
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password']);
        $user->setFirstname($userData['firstname']);
        $user->setLastname($userData['lastname']);
        $user->setRole(json_decode($userData['role'], true));

        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM user');
        $stmt->execute();
        $users = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $userData) {
            $user = new UserModel();
            $user->setId($userData['id']);
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setRole(json_decode($userData['role'], true));
            $users[] = $user;
        }
        return $users;
    }

    public function delete(int $userId)
    {
        $stmt = $this->db->prepare('DELETE FROM user WHERE id = :id');
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getUserById(int $id): ?UserModel
    {
      return $this->findOneById($id);
    }

    public function findOneByEmail(string $email)
    {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        $userModel = new UserModel();
        if ($user) {
            $userModel->setId($user['id']);
            $userModel->setEmail($user['email']);
            $userModel->setPassword($user['password']);
            $userModel->setFirstname($user['firstname']);
            $userModel->setLastname($user['lastname']);
            $userModel->setRole(json_decode($user['role'], true));
            return $userModel;
        } else {
            return false;
        }
    }

}
