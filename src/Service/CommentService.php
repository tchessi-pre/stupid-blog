<?php

namespace App\Service;

use App\Repository\CommentRepository;
use App\Model\CommentModel;

class CommentService
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function createComment($content, $postId, $userId)
    {
        $comment = new CommentModel();
        $comment->setContent($content);
        $comment->setPostId($postId);
        $comment->setUserId($userId);
        $comment->setCreatedAt(new \DateTime());

        $this->commentRepository->save($comment);
    }

    public function updateComment($commentId, $content)
    {
        $comment = $this->commentRepository->findOneById($commentId);
        if (!$comment) {
            throw new \Exception("Comment not found");
        }

        $comment->setContent($content);
        $this->commentRepository->save($comment);
    }

    public function deleteComment($commentId)
    {
        $comment = $this->commentRepository->findOneById($commentId);
        if (!$comment) {
            throw new \Exception("Comment not found");
        }

        $this->commentRepository->delete($commentId);
    }
}
