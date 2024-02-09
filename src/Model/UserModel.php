<?php

namespace App\Model;

use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Class\Database;

class UserModel
{
    private $id;
    private $email;
    private $password;
    private $firstname;
    private $lastname;
    private $role = [];
    private $posts = [];
    private $comments = [];

    public function __construct()
    {
        $this->posts = [];
        $this->comments = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function setRole(array $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getPosts(): array
    {
        if (empty($this->posts)) {
            $db = new Database();
            $connection = $db->getConnection();
            $postRepository = new PostRepository($connection);
            $this->posts = $postRepository->findByUser($this);
        }
        return $this->posts;
    }

    public function setPosts(array $posts): self
    {
        $this->posts = $posts;
        foreach ($posts as $post) {
            $post->setUserId($this->getId());
        }
        return $this;
    }

    public function getComments(): array
    {
        if (empty($this->comments)) {
            $db = new Database();
            $connection = $db->getConnection();
            $commentRepository = new CommentRepository($connection);
            $this->comments = $commentRepository->findByUser($this->id);
        }
        return $this->comments;
    }

    public function setComments(array $comments): self
    {
        $this->comments = $comments;
        foreach ($comments as $comment) {
            $comment->setUserId($this->getId());
        }
        return $this;
    }
}
