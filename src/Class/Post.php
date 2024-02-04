<?php

namespace App\Class;

use DateTime;

class Post
{

    public function __construct(
        private ?int $id = null,
        private ?string $title = null,
        private ?string $content = null,
        private ?DateTime $createdAt = null,
        private ?DateTime $updatedAt = null,
        private ?User $user = null,
        private ?array $comments = [],
        private ?Category $category = null,
    ) {
    }

    /**
     * getId
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * setId
     *
     * @param  mixed $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * getTitle
     * 
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * setTitle
     *
     * @param  mixed $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * getContent
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * setContent
     *
     * @param  mixed $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * getCreatedAt
     *
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * setCreatedAt
     *
     * @param  DateTime $createdAt
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * getUpdatedAt
     *
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * setUpdatedAt
     *
     * @param  DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * getUser
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * setUser
     *
     * @param  User $user
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * getComments
     *
     * @return array|null
     */
    public function getComments(): ?array
    {
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
            $comment->setPost($this);
        }

        return $this;
    }

    public function addComment(Comment $comment): self
    {
        if (!in_array($comment, $this->comments) && $comment->getPost()->getId() === $this->id) {
            $this->comments[] = $comment;
        }
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
            'user' => $this->user->getEmail(),
            'comments' => array_map(fn ($comment) => $comment->getId(), $this->comments),
            'category' => $this->category->getName()
        ];
    }

    public function save()
    {
        if (empty($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    public function findOneById(int $id): self
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM post WHERE id = :id');
        $query->bindValue(':id', $id, \PDO::PARAM_INT);
        $query->execute();
        $arrayPost = $query->fetch(\PDO::FETCH_ASSOC);
        $this->id = $arrayPost['id'];
        $this->title = $arrayPost['title'];
        $this->content = $arrayPost['content'];
        $this->createdAt = new DateTime($arrayPost['created_at']);
        $this->updatedAt = $arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null;
        $this->user = (new User())->findOneById($arrayPost['user_id']);
        $this->category = (new Category())->findOneById($arrayPost['category_id']);
        $this->comments = (new Comment())->findByPost($arrayPost['id']);
        return $this;
    }

    public function findAll()
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('SELECT * FROM post');
        $query->execute();
        $arrayPosts = $query->fetchAll(\PDO::FETCH_ASSOC);
        $results = [];
        foreach ($arrayPosts as $arrayPost) {
            $post = new Post();
            $post->setId($arrayPost['id']);
            $post->setTitle($arrayPost['title']);
            $post->setContent($arrayPost['content']);
            $post->setCreatedAt(new DateTime($arrayPost['created_at']));
            $post->setUpdatedAt($arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null);
            $post->setUser((new User())->findOneById($arrayPost['user_id']));
            $post->setCategory((new Category())->findOneById($arrayPost['category_id']));
            $post->setComments((new Comment())->findByPost($arrayPost['id']));
            $results[] = $post;
        }
        return $results;
    }

    public function insert()
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('INSERT INTO post (title, content, user_id, category_id, created_at) VALUES (:title, :content, :user_id, :category_id, NOW())');
        $query->bindValue(':title', $this->title, \PDO::PARAM_STR);
        $query->bindValue(':content', $this->content, \PDO::PARAM_STR);
        $query->bindValue(':user_id', $this->user->getId(), \PDO::PARAM_INT);
        $query->bindValue(':category_id', $this->category->getId(), \PDO::PARAM_INT);
        $query->execute();
        $this->id = $connection->lastInsertId();
    }

    public function update()
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('UPDATE post SET title = :title, content = :content, user_id = :user_id, category_id = :category_id, updated_at = NOW() WHERE id = :id');
        $query->bindValue(':id', $this->id, \PDO::PARAM_INT);
        $query->bindValue(':title', $this->title, \PDO::PARAM_STR);
        $query->bindValue(':content', $this->content, \PDO::PARAM_STR);
        $query->bindValue(':user_id', $this->user->getId(), \PDO::PARAM_INT);
        $query->bindValue(':category_id', $this->category->getId(), \PDO::PARAM_INT);
        $query->execute();
    }

    public function delete()
    {
        $connection = Database::getConnection();
        $query = $connection->prepare('DELETE FROM post WHERE id = :id');
        $query->bindValue(':id', $this->id, \PDO::PARAM_INT);
        $query->execute();
    }

    public function findByUser(User $user)
    {
        $sql = 'SELECT * FROM post WHERE user_id = :user_id';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $user->getId(), \PDO::PARAM_INT);
        $stmt->execute();
        $results = [];
        $arrayPost = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($arrayPost as $arrayPost) {
            $post = new Post();
            $post->setId($arrayPost['id']);
            $post->setTitle($arrayPost['title']);
            $post->setContent($arrayPost['content']);
            $post->setCreatedAt(new DateTime($arrayPost['created_at']));
            $post->setUpdatedAt($arrayPost['updated_at'] ?? new DateTime($arrayPost['updated_at']));
            $post->setUser((new User())->findOneById($arrayPost['user_id']));
            $post->setCategory((new Category())->findOneById($arrayPost['category_id']));
            $post->setComments((new Comment())->findByPost($arrayPost['id']));
            $results[] = $post;
        }
        return $results;
    }

    public function findAllPaginated(int $page)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sql = 'SELECT * FROM post ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $results = [];
        $arrayPost = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($arrayPost as $arrayPost) {
            $post = new Post();
            $post->setId($arrayPost['id']);
            $post->setTitle($arrayPost['title']);
            $post->setContent($arrayPost['content']);
            $post->setCreatedAt(new DateTime($arrayPost['created_at']));
            $post->setUpdatedAt($arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null);
            $post->setUser((new User())->findOneById($arrayPost['user_id']));
            $post->setCategory((new Category())->findOneById($arrayPost['category_id']));
            $post->setComments((new Comment())->findByPost($arrayPost['id']));
            $results[] = $post;
        }
        return $results;
    }
}
