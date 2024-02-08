<?php

namespace App\Service;

use App\Interface\ServiceInterface;
use App\Repository\CommentRepository;
use App\Model\CommentModel;

class CommentService implements ServiceInterface
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function create($data)
    {
        $content = $data['content'] ?? null;
        $postId = $data['post_id'] ?? null;
        $comment = new CommentModel();
        $comment->setContent($content);
        $comment->setPostId($postId);
        $comment->setUserId($_SESSION['user']->getId());
        $comment->setCreatedAt(new \DateTime());

        $this->commentRepository->save($comment);
    }

    public function update($commentId, $content)
    {
        $comment = $this->commentRepository->findOneById($commentId);
        if (!$comment) {
            throw new \Exception("Comment not found");
        }

        $comment->setContent($content);
        $this->commentRepository->save($comment);
    }

    public function delete($commentId)
    {
        $comment = $this->commentRepository->findOneById($commentId);
        if (!$comment) {
            throw new \Exception("Comment not found");
        }

        $this->commentRepository->delete($commentId);
    }

    public function getById($id)
    {
      return $this->commentRepository->findOneById($id);
    }
  
    public function getAll()
    {
      return $this->commentRepository->findAll();
    }

    public function toArray($comment): array
    {
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'user_id' => $comment->getUserId(),
            'post_id' => $comment->getPostId()
        ];
    }
}
