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
        // Initialisez les propriétés posts et comments dans le constructeur
        $this->posts = [];
        $this->comments = [];
    }

    // Getters et setters pour les autres propriétés

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
        // Assurez-vous que les posts sont récupérés uniquement s'ils ne sont pas déjà chargés
        if (empty($this->posts)) {
            // Logique pour récupérer les posts de l'utilisateur depuis la base de données
            $db = new Database();
            $connection = $db->getConnection();
            $postRepository = new PostRepository($connection);
            $this->posts = $postRepository->findByUser($this);
        }
        return $this->posts;
    }

    public function setPosts(array $posts): self
    {
        // Définissez les posts et assurez-vous de mettre à jour les IDs des posts
        $this->posts = $posts;
        foreach ($posts as $post) {
            $post->setUserId($this->getId());
        }
        return $this;
    }

    public function getComments(): array
    {
        // Assurez-vous que les commentaires sont récupérés uniquement s'ils ne sont pas déjà chargés
        if (empty($this->comments)) {
            // Logique pour récupérer les commentaires de l'utilisateur depuis la base de données
            $db = new Database();
            $connection = $db->getConnection();
            $commentRepository = new CommentRepository($connection);
            $this->comments = $commentRepository->findByUser($this->id);
        }
        return $this->comments;
    }

    public function setComments(array $comments): self
    {
        // Définissez les commentaires et assurez-vous de mettre à jour les IDs des commentaires
        $this->comments = $comments;
        foreach ($comments as $comment) {
            $comment->setUserId($this->getId());
        }
        return $this;
    }
}
