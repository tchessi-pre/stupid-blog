<?php

namespace App\Model;

use App\Repository\CommentRepository;
use App\Class\Database;
use App\Repository\PostRepository;

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
     * getPosts
     * 
     * @return array Post[]
     */
    public function getPosts(): array
    {
        $db = new Database();
        $connection = $db->getConnection();
        if (empty($this->posts)) {
            $postRepository = new PostRepository($connection);
            $this->posts = $postRepository->findByUser($this);
        }
        return $this->posts;
    }

        /**
     * setComments
     *
     * @param  array $comments
     * @return self
     */
    public function setPosts(array $posts)
    {
        $this->posts = $posts;
        foreach ($posts as $post) {
            $post->setPostId($this->getId());
        }

        return $this;
    }

    public function getComments(): array
    {
        $db = new Database();
        $connection = $db->getConnection();
        if (empty($this->comments)) {
            $commentRepository = new CommentRepository($connection);
            $this->comments = $commentRepository->findByUser($this->id); 
        }
        return $this->comments;
    }

        /**
     * setComments
     *
     * @param  array $comments
     * @return self
     */
    public function setComments(array $comments): self
    {
        $this->comments = $comments;
        foreach ($comments as $comment) {
            $comment->setPostId($this->getId());
        }

        return $this;
    }
}