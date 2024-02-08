<?php

namespace App\Repository;

use PDO;
use App\Model\CommentModel;
use DateTime;
use App\Interface\RepositoryInterface;

class CommentRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save($comment)
    {
        if (null === $comment->getId()) {
            $this->insert($comment);
        } else {
            $this->update($comment);
        }
    }

    public function insert($comment)
    {
        $stmt = $this->db->prepare("INSERT INTO comment (content, created_at, user_id, post_id) VALUES (:content, :created_at, :user_id, :post_id)");
        $stmt->execute([
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'user_id' => $comment->getUserId(),
            'post_id' => $comment->getPostId()
        ]);
        $comment->setId($this->db->lastInsertId());
    }

    public function update($comment)
    {
        $stmt = $this->db->prepare("UPDATE comment SET content = :content, created_at = :created_at, user_id = :user_id, post_id = :post_id WHERE id = :id");
        $stmt->execute([
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'user_id' => $comment->getUserId(),
            'post_id' => $comment->getPostId(),
            'id' => $comment->getId(),
        ]);
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

    public function delete(int $commentId)
    {
        $stmt = $this->db->prepare('DELETE FROM comment WHERE id = :commentId');
        $stmt->bindValue(':commentId', $commentId, \PDO::PARAM_INT);
        $stmt->execute();
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


}
