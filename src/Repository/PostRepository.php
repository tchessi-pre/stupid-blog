<?php

namespace App\Repository;

use PDO;
use App\Model\AppModel;
use App\Class\User;
use DateTime;

class PostRepository
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function save($post)
  {
    if (empty($this->id)) {
      $connection = Database::getConnection();
      $query = $connection->prepare('INSERT INTO post (title, content, user_id, category_id, created_at) VALUES (:title, :content, :user_id, :category_id, NOW())');
      $query->bindValue(':title', $this->title, \PDO::PARAM_STR);
      $query->bindValue(':content', $this->content, \PDO::PARAM_STR);
      $query->bindValue(':user_id', $this->user->getId(), \PDO::PARAM_INT);
      $query->bindValue(':category_id', $this->category->getId(), \PDO::PARAM_INT);
      $query->execute();
      $this->id = $connection->lastInsertId();
    } else {
      $connection = Database::getConnection();
        $query = $connection->prepare('UPDATE post SET title = :title, content = :content, user_id = :user_id, category_id = :category_id, updated_at = NOW() WHERE id = :id');
        $query->bindValue(':id', $this->id, \PDO::PARAM_INT);
        $query->bindValue(':title', $this->title, \PDO::PARAM_STR);
        $query->bindValue(':content', $this->content, \PDO::PARAM_STR);
        $query->bindValue(':user_id', $this->user->getId(), \PDO::PARAM_INT);
        $query->bindValue(':category_id', $this->category->getId(), \PDO::PARAM_INT);
        $query->execute();
    }
  }

  public function delete()
  {
    $connection = Database::getConnection();
    $query = $connection->prepare('DELETE FROM post WHERE id = :id');
    $query->bindValue(':id', $this->id, \PDO::PARAM_INT);
    $query->execute();
  }

  public function toArray()
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

  public function findOneById($id)
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
    $this->comments = (new CommentRepository($connection))->findByPost($arrayPost['id']);
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
        $post->setComments((new CommentRepository($connection))->findByPost($arrayPost['id']));
        $results[] = $post;
    }
    return $results;
  }

  public function findByUser($user)
  {
    $sql = 'SELECT * FROM post WHERE user_id = :user_id';
    $connection = Database::getConnection();
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
        $post->setComments((new CommentRepository($connection))->findByPost($arrayPost['id']));
        $results[] = $post;
    }
    return $results;
  }

  public function findAllPaginated($page)
  {
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $sql = 'SELECT * FROM post ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
    $connection = Database::getConnection();
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
        $post->setComments((new CommentRepository($connection))->findByPost($arrayPost['id']));
        $results[] = $post;
    }
    return $results;
  }

}