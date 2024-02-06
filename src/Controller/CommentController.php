<?php

namespace App\Controller;

use App\Service\CommentService;
use App\Class\Redirector;
use App\View\ViewRenderer;

class CommentController
{
    private $commentService;
    private $viewRenderer;
    private $redirector;

    public function __construct(CommentService $commentService, ViewRenderer $viewRenderer, Redirector $redirector)
    {
        $this->commentService = $commentService;
        $this->viewRenderer = $viewRenderer;
        $this->redirector = $redirector;
    }

    public function create($request)
    {
        // var_dump($_SESSION);die;
        $postId = $request['post_id'] ?? null;
        $content = $request['content'] ?? '';
        $userId = $_SESSION['user']->getId();
        // var_dump($content);
        // die;
        
        if (is_null($postId) || is_null($userId) || empty($content)) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => 'Commentaire invalide']);
            return;
        }

        try {
            $this->commentService->createComment($content, $postId, $userId);
            $this->redirector->redirect('post', ['id' => $postId]);
        } catch (\Exception $e) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => $e->getMessage()]);
        }
    }

}
