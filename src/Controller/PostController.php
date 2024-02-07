<?php

namespace App\Controller;

use App\Service\PostService;
use App\Class\Database;
use App\Repository\PostRepository;
use App\Class\Redirector;
use App\View\ViewRenderer;
use App\Interface\ControllerInterface;

class PostController implements ControllerInterface
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

  public function update($request)
  {
    $postId = $request['id'] ?? null;
    $categoryId = $request['category'] ?? null;
    $content = $request['content'] ?? '';
    $title = $request['title'] ?? '';
    $userId = $_SESSION['user']->getId();

    if (is_null($postId) || is_null($categoryId) || is_null($userId) || empty($content) || empty($title)) {
      $this->redirector->redirect('posts', ['1', 'error' => 'Données de publication invalides']);
      return;
    }
    try {
    } catch (\Exception $e) {
      $this->redirector->redirect('posts', ['1', 'error' => $e->getMessage()]);
    }
  }

  public function delete($request)
  {
    $postId = $request['id'] ?? null;
    if (is_null($postId)) {
      $this->redirector->redirect('posts', ['1', 'error' => 'ID de publication invalide']);
      return;
    }
    try {
    } catch (\Exception $e) {
      $this->redirector->redirect('posts', ['1', 'error' => $e->getMessage()]);
    }
  }


  public function paginatedPosts($page)
  {
    $viewRenderer = new ViewRenderer;
    $db = new Database();
    $connection = $db->getConnection();
    $post = new PostRepository($connection);
    $posts = $post->findAllPaginated($page);
    $pages = count($post->findAll()) / 10;
    $viewRenderer->render('posts', ['posts' => $posts, 'pages' => $pages]);
  }

  public function viewPost($id, $error = null)
  {
    if (is_numeric($id) === false) {
      throw new \Exception("L'identifiant du post n'est pas valide");
      return;
    }
    $viewRenderer = new ViewRenderer;
    $db = new Database();
    $connection = $db->getConnection();
    $post = new PostRepository($connection);
    $post = $post->findOneById((int) $id);
    $viewRenderer->render('post', ['post' => $post, 'error' => $error]);
  }
}
