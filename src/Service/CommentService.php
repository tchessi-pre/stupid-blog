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
//  if (empty($content)) {
//             throw new \Exception("Le contenu ne peut pas être vide");

//             return;
//         }

//         if (self::getUser() === null) {
//             throw new \Exception("Vous devez être connecté pour commenter");

//             return;
//         }

//         if (is_numeric($post_id) === false) {
//             throw new \Exception("L'identifiant du post n'est pas valide");

//             return;
//         }

//         $post_id = (int) $post_id;

//         $post = new Post();
//         $post = $post->findOneById($post_id);

//         $comment = new Comment();
//         $comment->setContent($content);
//         $comment->setUser(self::getUser());
//         $comment->setPost($post);
//         $comment->setCreatedAt(new \DateTime());
//         $comment->save();

//         $this->redirect('post', ['id' => $post->getId()]);