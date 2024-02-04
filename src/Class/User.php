<?php

namespace App\Class;

class User
{

    public function __construct(
        private ?int $id = null,
        private ?string $email = null,
        private ?string $password = null,
        private ?string $firstname = null,
        private ?string $lastname = null,
        private ?array $role = [],
        private ?array $posts = [],
        private ?array $comments = []
    ) {
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of firstname
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole(): array
    {
        return $this->role;
    }

    /**
     * Set the value of role
     * 
     * @param array $role
     * @return  self
     */
    public function setRole(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * addRole
     *
     * @param string $role
     * @return self
     */
    public function addRole(string $role): self
    {
        $this->role[] = $role;

        return $this;
    }

    /**
     * removeRole
     *
     * @param string $role
     * @return self
     */
    public function removeRole(string $role): self
    {
        $key = array_search($role, $this->role);
        if ($key !== false) {
            unset($this->role[$key]);
        }

        return $this;
    }

    /**
     * getPosts
     * 
     * @return array Post[]
     */
    public function getPosts(): array
    {
        if (empty($this->posts)) {
            $postModel = new Post();
            $this->posts = $postModel->findByUser($this);
        }
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!in_array($post, $this->posts) && $post->getUser()->getId() === $this->id && [] !== $this->getPosts()) {
            $this->posts[] = $post;
        } else {
            $this->getPosts();
            $this->posts[] = $post;
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        $key = array_search($post, $this->posts);
        if ($key !== false) {
            unset($this->posts[$key]);
        }

        return $this;
    }

    public function getComments(): array
    {
        if (empty($this->comments)) {
            $commentModel = new Comment();
            $this->comments = $commentModel->findByUser($this->id); 
        }
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        $this->comments[] = $comment;

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        $key = array_search($comment, $this->comments);
        if ($key !== false) {
            unset($this->comments[$key]);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->role);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'role' => $this->role
        ];
    }

    public function findOneById(int $id): self
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM user WHERE id = :id');
        $query->bindValue(':id', $id, \PDO::PARAM_INT);
        $query->execute();
        $user = $query->fetch(\PDO::FETCH_ASSOC);
        $this->id = $user['id'];
        $this->email = $user['email'];
        $this->password = $user['password'];
        $this->firstname = $user['firstname'];
        $this->lastname = $user['lastname'];
        $this->role = json_decode($user['role'], true);

        return $this;
    }

    public function findAll(): array
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM user');
        $query->execute();
        $users = $query->fetchAll(\PDO::FETCH_ASSOC);
        $usersArray = [];
        foreach ($users as $user) {
            $userObject = new User();
            $userObject->setId($user['id']);
            $userObject->setEmail($user['email']);
            $userObject->setPassword($user['password']);
            $userObject->setFirstname($user['firstname']);
            $userObject->setLastname($user['lastname']);
            $userObject->setRole(json_decode($user['role'], true));
            $usersArray[] = $userObject;
        }
        return $usersArray;
    }

    public function save()
    {
        if (null === $this->id) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    private function insert()
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('INSERT INTO user (email, password, firstname, lastname, role) VALUES (:email, :password, :firstname, :lastname, :role)');
        $query->bindValue(':email', $this->email, \PDO::PARAM_STR);
        $query->bindValue(':password', $this->password, \PDO::PARAM_STR);
        $query->bindValue(':firstname', $this->firstname, \PDO::PARAM_STR);
        $query->bindValue(':lastname', $this->lastname, \PDO::PARAM_STR);
        $query->bindValue(':role', json_encode($this->role), \PDO::PARAM_STR);
        $query->execute();
        $this->id = $connection->lastInsertId();
    }

    private function update()
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('UPDATE user SET email = :email, password = :password, firstname = :firstname, lastname = :lastname, role = :role WHERE id = :id');
        $query->bindValue(':id', $this->id, \PDO::PARAM_INT);
        $query->bindValue(':email', $this->email, \PDO::PARAM_STR);
        $query->bindValue(':password', $this->password, \PDO::PARAM_STR);
        $query->bindValue(':firstname', $this->firstname, \PDO::PARAM_STR);
        $query->bindValue(':lastname', $this->lastname, \PDO::PARAM_STR);
        $query->bindValue(':role', json_encode($this->role), \PDO::PARAM_STR);
        $query->execute();
    }

    public function findOneByEmail(string $email)
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM user WHERE email = :email');
        $query->bindValue(':email', $email, \PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            $this->id = $user['id'];
            $this->email = $user['email'];
            $this->password = $user['password'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            $this->role = json_decode($user['role'], true);
            return $this;
        } else {
            return false;
        }
    }
}
