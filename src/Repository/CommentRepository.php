<?php

namespace App\Repository;

use PDO;

class CommentRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save($comment)
    {
        $stmt = $this->db->prepare("INSERT INTO comment (content, user_id, post_id, created_at) VALUES (:content, :user_id, :post_id, :created_at)");
        $stmt->execute([
            'content' => $comment->getContent(),
            'user_id' => $comment->getUserId(),
            'post_id' => $comment->getPostId(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }
}
