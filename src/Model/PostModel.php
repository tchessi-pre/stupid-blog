<?php 

namespace App\Model;

use App\Model\CommentModel;

class PostModel
{
  private $id;
  private $title;
  private $content;
  private $createdAt;
  private $updatedAt;
  private $userId;
  private $comments;
  private $categoryId;

  public function __construct()
  {
  }

  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  public function getContent()
  {
    return $this->content;
  }

  public function setContent($content)
  {
    $this->content = $content;
  }

  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
  }

  public function getUserId()
  {
    return $this->userId;
  }

  public function setUserId($userId)
  {
    $this->userId = $userId;
  }

  public function getComments()
  {
    return $this->comments;
  }

  public function setComments($comments)
  {
    $this->comments = $comments;
    foreach ($comments as $comment) {
      $comment->setPostId($this->getId());
    }

    return $this;
  }

  public function addComment(CommentModel $comment)
  {
    if (!in_array($comment, $this->comments) && $comment->getPostId() === $this->id) {
      $this->comments[] = $comment;
    }
    $this->comments[] = $comment;

    return $this;
  }

  public function removeComment(CommentModel $comment)
  {
    $key = array_search($comment, $this->comments);
    if ($key !== false) {
      unset($this->comments[$key]);
    }

    return $this;
  }

  public function getCategoryId()
  {
    return $this->categoryId;
  }

  public function setCategoryId($categoryId)
  {
    $this->categoryId = $categoryId;

    return $this;
  }

}