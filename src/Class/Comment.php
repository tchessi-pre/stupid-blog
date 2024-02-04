<?php

namespace App\Class;

use DateTime;

class Comment
{

    public function __construct(
        private ?int $id = null,
        private ?string $content = null,
        private ?DateTime $createdAt = null,
        private ?User $user = null,
        private ?Post $post = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Comment
    {
        $this->id = $id;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime|string $createdAt): Comment
    {
        if (is_string($createdAt)) {
            $createdAt = new DateTime($createdAt);
        }
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): Comment
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): Comment
    {
        $this->post = $post;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'user_id' => $this->user->getId(),
            'post_id' => $this->post->getId()
        ];
    }

    public function findOneById(int $id): self
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('SELECT * FROM comment WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);
        $comment = $query->fetch();
        if ($comment) {
            $this->id = $comment['id'];
            $this->content = $comment['content'];
            $this->createdAt = new DateTime($comment['created_at']);
            $this->user = (new User())->findOneById($comment['user_id']);
            $this->post = (new Post())->findOneById($comment['post_id']);
        }
        return $this;
    }

    public function findAll()
    {
        $pdo = Database::getConnection();
        $query = $pdo->query('SELECT * FROM comment');
        $comments = [];
        foreach ($query->fetchAll() as $comment) {
            $comments[] = (new Comment())
                ->setId($comment['id'])
                ->setContent($comment['content'])
                ->setCreatedAt($comment['created_at'])
                ->setUser((new User())->findOneById($comment['user_id']))
                ->setPost((new Post())->findOneById($comment['post_id']));
        }
        return $comments;
    }

    public function findByPost(int $id)
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('SELECT * FROM comment WHERE post_id = :id');
        $query->execute([
            'id' => $id
        ]);
        $comments = [];
        foreach ($query->fetchAll() as $comment) {
            $comments[] = (new Comment())
                ->setId($comment['id'])
                ->setContent($comment['content'])
                ->setCreatedAt($comment['created_at'])
                ->setUser((new User())->findOneById($comment['user_id']));
        }
        return $comments;
    }

    public function findByUser(int $id)
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('SELECT * FROM comment WHERE user_id = :id');
        $query->execute([
            'id' => $id
        ]);
        $comments = [];
        foreach ($query->fetchAll() as $comment) {
            $comments[] = (new Comment())
                ->setId($comment['id'])
                ->setContent($comment['content'])
                ->setCreatedAt($comment['created_at'])
                ->setPost((new Post())->findOneById($comment['post_id']));
        }
        return $comments;
    }

    public function save()
    {
        if (is_null($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    private function insert()
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('INSERT INTO comment (content, created_at, user_id, post_id) VALUES (:content, :created_at, :user_id, :post_id)');
        $query->execute([
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'user_id' => $this->user->getId(),
            'post_id' => $this->post->getId()
        ]);
        $this->id = $pdo->lastInsertId();
    }

    private function update()
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('UPDATE comment SET content = :content, created_at = :created_at, user_id = :user_id, post_id = :post_id WHERE id = :id');
        $query->execute([
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'user_id' => $this->user->getId(),
            'post_id' => $this->post->getId(),
            'id' => $this->id
        ]);
    }
}
