<?php

namespace App\Repository;

use App\Class\Database;
use PDO;
use App\Model\UserModel;

class UserRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
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
        $user->setRole(json_decode($userData['role']), true);

        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM user');
        $stmt->execute();
        $users = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $userData) {
            $user = new UserModel();
            $user->setId($user['id']);
            $user->setEmail($user['email']);
            $user->setPassword($user['password']);
            $user->setFirstname($user['firstname']);
            $user->setLastname($user['lastname']);
            $user->setRole(json_decode($user['role'], true));
            $users[] = $user;
        }
        return $users;
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
            $userModel->setEmail ($user['email']);
            $userModel->setPassword ($user['password']);
            $userModel->setFirstname ($user['firstname']);
            $userModel->setLastname ($user['lastname']);
            $userModel->setRole (json_decode($user['role'], true));
            return $userModel;
        } else {
            return false;
        }
    }
    public function save(UserModel $user)
    {
        if (null === $user->getId()) {
            $this->insert($user);
        } else {
            $this->update($user);
        }
    }

    private function insert(UserModel $user)
    {
        $connection = Database::getConnection();
        $stmt = $this->db->prepare('INSERT INTO user (email, password, firstname, lastname, role) VALUES (:email, :password, :firstname, :lastname, :role)');
        $stmt->bindValue(':email', $user->getEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), \PDO::PARAM_STR);
        $stmt->bindValue(':firstname', $user->getFirstname(), \PDO::PARAM_STR);
        $stmt->bindValue(':lastname', $user->getLastname(), \PDO::PARAM_STR);
        $stmt->bindValue(':role', json_encode($user->getRole()), \PDO::PARAM_STR);
        $stmt->execute();
        $user->setId($connection->lastInsertId());
    
    }

    private function update(UserModel $user)
    {
        $stmt = $this->db->prepare('UPDATE user SET email = :email, password = :password, firstname = :firstname, lastname = :lastname, role = :role WHERE id = :id');
        $stmt->bindValue(':email', $user->getEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), \PDO::PARAM_STR);
        $stmt->bindValue(':firstname', $user->getFirstname(), \PDO::PARAM_STR);
        $stmt->bindValue(':lastname', $user->getLastname(), \PDO::PARAM_STR);
        $stmt->bindValue(':role', json_encode($user->getRole()), \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function delete(int $userId)
    {
        $stmt = $this->db->prepare('DELETE FROM user WHERE id = :id');
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
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

    
}