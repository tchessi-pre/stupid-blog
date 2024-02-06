<?php

namespace App\Service;

use App\Repository\PostRepository;
use App\Model\PostModel;

class PostService
{
  private $postRepository;

  public function __construct(PostRepository $postRepository)
  {
    $this->postRepository = $postRepository;
  }

  public function createPost($title, $content, $userId,  $categoryId)
  {
    $post = new PostModel();
    $post->setTitle($title);
    $post->setContent($content);
    $post->setUserId($userId);
    $post->setCategoryId($categoryId);
    $post->setCreatedAt(new \DateTime());

    $this->postRepository->save($post);
  }
}