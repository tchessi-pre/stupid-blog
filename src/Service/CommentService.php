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
}
