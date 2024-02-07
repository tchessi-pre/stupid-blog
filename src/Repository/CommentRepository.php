<?php

namespace App\Repository;

use PDO;
use App\Model\CommentModel;
// use App\Class\Comment;
use App\Class\User;
use App\Class\Post;
use DateTime;
use App\Class\Database;

class CommentRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(CommentModel $comment)
    {
        if (null === $comment->getId()) {
            $this->insert($comment);
        } else {
            $this->update($comment);
        }
    }

    private function insert(CommentModel $comment)
    {
        $connection = Database::getConnection();
        $stmt = $this->db->prepare("INSERT INTO comment (content, created_at, user_id, post_id) VALUES (:content, :created_at, :user_id, :post_id)");
        $stmt->execute([
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'user_id' => $comment->getUserId(),
            'post_id' => $comment->getPostId()
        ]);
        $comment->setId($connection->lastInsertId());


        // $pdo = Database::getConnection();
        // $query = $pdo->prepare('INSERT INTO comment (content, created_at, user_id, post_id) VALUES (:content, :created_at, :user_id, :post_id)');
        // $query->execute([
        //     'content' => $this->content,
        //     'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        //     'user_id' => $this->user->getId(),
        //     'post_id' => $this->post->getId()
        // ]);
        // $this->id = $pdo->lastInsertId();
    
    }

    private function update(CommentModel $comment)
    {
        $connection = Database::getConnection();
        $stmt = $this->db->prepare("UPDATE comment SET content = :content, created_at = :created_at, user_id = :user_id, post_id = :post_id WHERE id = :id");
        $stmt->execute([
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'user_id' => $comment->getUserId(),
            'post_id' => $comment->getPostId(),
            'id' => $comment->getId(),
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


    public function findOneById(int $commentId): ?CommentModel
    {
        $stmt = $this->db->prepare('SELECT * FROM comment WHERE id = :id');
        $stmt->execute(['id' => $commentId]);
    
        $commentData = $stmt->fetch();
        if (!$commentData) {
            return null;
        }
    
        $comment = new CommentModel();
        $comment->setId($commentData['id']);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new DateTime($commentData['created_at']));
    
        $comment->setUserId($commentData['user_id']);
        $comment->setPostId($commentData['post_id']);
    
        return $comment;
    }
    

    public function findAll(): array
{

    $stmt = $this->db->prepare('SELECT * FROM comment');
    $stmt->execute();

    $comments = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $commentData) {
        $comment = new CommentModel();
        $comment->setId($commentData['id']);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new DateTime($commentData['created_at']));

        $comment->setUserId($commentData['user_id']);
        $comment->setPostId($commentData['post_id']);

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
        $comment = new CommentModel();
        $comment->setId($commentData['id']);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new DateTime($commentData['created_at']));

        $comment->setUserId($commentData['user_id']);

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
        $comment = new CommentModel();
        $comment->setId($commentData['id']);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new DateTime($commentData['created_at']));

        $comment->setPostId($commentData['post_id']);

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
