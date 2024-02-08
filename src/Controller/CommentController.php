<?php

namespace App\Controller;

use App\Service\CommentService;
use App\Class\Redirector;
use App\View\ViewRenderer;
use App\Interface\ControllerInterface;

class CommentController implements ControllerInterface
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
        // var_dump($request);die;
        $postId = $request['post_id'] ?? null;
        $content = $request['content'] ?? '';

        if (is_null($postId) || is_null($_SESSION['user']->getId()) || empty($content)) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => 'Commentaire invalide']);
            return;
        }

        try {
            $this->commentService->create($request);
            $this->redirector->redirect('post', ['id' => $postId]);
        } catch (\Exception $e) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => $e->getMessage()]);
        }
    }

    public function update($request)
    {
        $commentId = $request['comment_id'] ?? null;
        $content = $request['content'] ?? '';
        $postId = $request['post_id'] ?? null;

        if (is_null($commentId) || is_null($postId) || empty($content)) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => 'Données de commentaire non valides']);
            return;
        }

        try {
            $this->commentService->update($commentId, $content);
            $this->redirector->redirect('post', ['id' => $postId, 'success' => 'Commentaire mis à jour avec succès']);
        } catch (\Exception $e) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => $e->getMessage()]);
        }
    }

    public function delete($request)
    {
        $commentId = $request['comment_id'] ?? null;
        $postId = $request['post_id'] ?? null;

        if (is_null($commentId) || is_null($postId)) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => 'ID de commentaire invalide']);
            return;
        }

        try {
            $this->commentService->delete($commentId);
            $this->redirector->redirect('post', ['id' => $postId, 'success' => 'Commentaire supprimé avec succès']);
        } catch (\Exception $e) {
            $this->redirector->redirect('post', ['id' => $postId, 'error' => $e->getMessage()]);
        }
    }
}
