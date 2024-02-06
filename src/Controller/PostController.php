<?php

namespace App\Controller;

use App\Service\PostService;
use App\Class\Redirector;
use App\View\ViewRenderer;

class PostController
{
  private $postService;
  private $viewRenderer;
  private $redirector;

  public function __construct(PostService $postService, ViewRenderer $viewRenderer, Redirector $redirector)
  {
    $this->postService = $postService;
    $this->viewRenderer = $viewRenderer;
    $this->redirector = $redirector;
  }

  public function create($request)
  {
    $categoryId = $request['category'] ?? null;
    $content = $request['content'] ?? '';
    $title = $request['title'] ?? '';
    $userId = $_SESSION['user']->getId();

    if (is_null($categoryId) || is_null($userId) || empty($content) || empty($title)) {
      $this->redirector->redirect('posts', ['1', 'error' => 'Article invalide']);
      return;
    }

    try {
      $this->postService->createPost($title, $content, $userId, $categoryId);
      $this->redirector->redirect('posts', ['1']);
    } catch (\Exception $e) {
      $this->redirector->redirect('posts', ['1', 'error' => $e->getMessage()]);
    }
  }
}