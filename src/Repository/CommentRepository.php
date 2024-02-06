<?php

namespace App\Repository;

use PDO;
use App\Class\Comment;
use App\Class\User;
use App\Class\Post;
use DateTime;

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


    public function findOneById(int $commentId): ?Comment
    {
        $stmt = $this->db->prepare('SELECT * FROM comment WHERE id = :id');
        $stmt->execute(['id' => $commentId]);
    
        $commentData = $stmt->fetch();
        if (!$commentData) {
            return null;
        }
    
        $comment = new Comment();
        $comment->setId($commentData['id']);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new DateTime($commentData['created_at']));
    
        $comment->setUser((new User())->findOneById($commentData['user_id']));
        $comment->setPost((new Post())->findOneById($commentData['post_id']));
    
        return $comment;
    }
    

    public function findAll(): array
{

    $stmt = $this->db->prepare('SELECT * FROM comment');
    $stmt->execute();

    $comments = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $commentData) {
        $comment = new Comment();
        $comment->setId($commentData['id'])
                ->setContent($commentData['content'])
                ->setCreatedAt(new DateTime($commentData['created_at']));

        $comment->setUser((new User())->findOneById($commentData['user_id']));
        $comment->setPost((new Post())->findOneById($commentData['post_id']));

        $comments[] = $comment;
    }

    return $comments;
}


    public function findByPost(int $postId): array
{

    $stmt = $this->db->prepare('SELECT * FROM comment WHERE post_id = :postId');
    $stmt->execute(['postId' => $postId]);


    $comments = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $commentData) {
        $comment = new Comment();
        $comment->setId($commentData['id'])
                ->setContent($commentData['content'])
                ->setCreatedAt(new DateTime($commentData['created_at']));

        $comment->setUser((new User())->findOneById($commentData['user_id']));

        $comments[] = $comment;
    }

    return $comments;
}


    public function findByUser(int $userId): array
{

    $stmt = $this->db->prepare('SELECT * FROM comment WHERE user_id = :userId');
    $stmt->execute(['userId' => $userId]);

    $comments = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $commentData) {
        $comment = new Comment();
        $comment->setId($commentData['id'])
                ->setContent($commentData['content'])
                ->setCreatedAt(new DateTime($commentData['created_at']));

        $comment->setPost((new Post())->findOneById($commentData['post_id']));

        $comments[] = $comment;
    }

    return $comments;
}


    public function delete(int $commentId)
    {
        $stmt = $this->db->prepare('DELETE FROM comment WHERE id = :commentId');
        $stmt->bindValue(':commentId', $commentId, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
