<?php

namespace App\Repository;

use PDO;
use App\Model\PostModel;
use App\Repository\CommentRepository;
use DateTime;
use App\Interface\RepositoryInterface;

class PostRepository implements RepositoryInterface
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function save($model)
  {
    $post = $model;
    if (empty($post->getId())) {
      $this->insert($post);
    } else {
      $this->update($post);
    }
  }

  public function insert(PostModel $post)
  {
    $stmt = $this->db->prepare('INSERT INTO post (title, content, user_id, category_id, created_at) VALUES (:title, :content, :user_id, :category_id, NOW())');
    $stmt->bindValue(':title', $post->getTitle(), \PDO::PARAM_STR);
    $stmt->bindValue(':content', $post->getContent(), \PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $post->getUserId(), \PDO::PARAM_INT);
    $stmt->bindValue(':category_id', $post->getCategoryId(), \PDO::PARAM_INT);
    $stmt->execute();
    $post->setId($this->db->lastInsertId());
  }

  public function update(PostModel $post)
  {
    $stmt = $this->db->prepare('UPDATE post SET title = :title, content = :content, user_id = :user_id, category_id = :category_id, updated_at = NOW() WHERE id = :id');
    $stmt->bindValue(':id', $post->getId(), \PDO::PARAM_INT);
    $stmt->bindValue(':title', $post->getTitle(), \PDO::PARAM_STR);
    $stmt->bindValue(':content', $post->getContent(), \PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $post->getUserId(), \PDO::PARAM_INT);
    $stmt->bindValue(':category_id', $post->getCategoryId(), \PDO::PARAM_INT);
    $stmt->execute();
  }

  public function delete(int $postId)
  {
    $stmt = $this->db->prepare('DELETE FROM post WHERE id = :id');
    $stmt->bindValue(':id', $postId, \PDO::PARAM_INT);
    $stmt->execute();
  }

  public function toArray(PostModel $post): array
  {
    return [
      'id' => $post->getId(),
      'title' => $post->getTitle(),
      'content' => $post->getContent(),
      'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
      'updated_at' => $post->getUpdatedAt() ? $post->getUpdatedAt()->format('Y-m-d H:i:s') : null,
      'user' => 'Placeholder for user', // $post->getUser()->getEmail(),
      'comments' => array_map(fn ($comment) => $comment->getId(), $post->getComments()),
      'category' => 'Placeholder for category' // $post->getCategory()->getName()
    ];
  }

  public function findOneById(int $id): ?PostModel
  {
    $stmt = $this->db->prepare('SELECT * FROM post WHERE id = :id');
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->execute();

    $arrayPost = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$arrayPost) {
      return null;
    }

    $post = new PostModel();
    $commentRepository = new CommentRepository($this->db);
    $post->setId($arrayPost['id']);
    $post->setTitle($arrayPost['title']);
    $post->setContent($arrayPost['content']);
    $post->setCreatedAt(new DateTime($arrayPost['created_at']));
    $post->setUpdatedAt($arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null);
    $post->setUserId($arrayPost['user_id']);
    $post->setCategoryId($arrayPost['category_id']);
    $post->setComments($commentRepository->findByPost($arrayPost['id']));
    return $post;
  }

  public function findAll(): array
  {
    $stmt = $this->db->prepare('SELECT * FROM post');
    $stmt->execute();

    $arrayPosts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $commentRepository = new CommentRepository($this->db);
    $results = [];
    foreach ($arrayPosts as $arrayPost) {
      $post = new PostModel();
      $post->setId($arrayPost['id']);
      $post->setTitle($arrayPost['title']);
      $post->setContent($arrayPost['content']);
      $post->setCreatedAt(new DateTime($arrayPost['created_at']));
      $post->setUpdatedAt($arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null);
      $post->setUserId($arrayPost['user_id']);
      $post->setCategoryId($arrayPost['category_id']);
      $post->setComments($commentRepository->findByPost($arrayPost['id']));
      $results[] = $post;
    }
    return $results;
  }

  public function findByUser($user): array
  {
    $stmt = $this->db->prepare('SELECT * FROM post WHERE user_id = :user_id');
    $stmt->bindValue(':user_id', $user->getId(), \PDO::PARAM_INT);
    $stmt->execute();

    $results = [];
    $commentRepository = new CommentRepository($this->db);
    $arrayPost = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($arrayPost as $arrayPost) {
      $post = new PostModel();
      $post->setId($arrayPost['id']);
      $post->setTitle($arrayPost['title']);
      $post->setContent($arrayPost['content']);
      $post->setCreatedAt(new DateTime($arrayPost['created_at']));
      $post->setUpdatedAt($arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null);
      $post->setUserId($arrayPost['user_id']);
      $post->setCategoryId($arrayPost['category_id']);
      $post->setComments($commentRepository->findByPost($arrayPost['id']));
      $results[] = $post;
    }
    return $results;
  }

  public function findAllPaginated($page): array
  {
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $stmt = $this->db->prepare('SELECT * FROM post ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    $results = [];
    $commentRepository = new CommentRepository($this->db);
    $arrayPost = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($arrayPost as $arrayPost) {
      $post = new PostModel();
      $post->setId($arrayPost['id']);
      $post->setTitle($arrayPost['title']);
      $post->setContent($arrayPost['content']);
      $post->setCreatedAt(new DateTime($arrayPost['created_at']));
      $post->setUpdatedAt($arrayPost['updated_at'] ? new DateTime($arrayPost['updated_at']) : null);
      $post->setUserId($arrayPost['user_id']);
      $post->setCategoryId($arrayPost['category_id']);
      $post->setComments($commentRepository->findByPost($arrayPost['id']));
      $results[] = $post;
    }
    return $results;
  }
}
