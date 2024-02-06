<?php

// namespace App\Model;

// use DateTime;
// use App\Class\User;
// use App\Class\Post;

// class CommentModel
// {

//     public function __construct(
//         private ?int $id = null,
//         private ?string $content = null,
//         private ?DateTime $createdAt = null,
//         private ?User $user = null,
//         private ?Post $post = null
//     ) {
//     }

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function setId(int $id): CommentModel
//     {
//         $this->id = $id;

//         return $this;
//     }

//     public function getContent(): ?string
//     {
//         return $this->content;
//     }

//     public function setContent(string $content): CommentModel
//     {
//         $this->content = $content;

//         return $this;
//     }

//     public function getCreatedAt(): ?DateTime
//     {
//         return $this->createdAt;
//     }

//     public function setCreatedAt(DateTime|string $createdAt): CommentModel
//     {
//         if (is_string($createdAt)) {
//             $createdAt = new DateTime($createdAt);
//         }
//         $this->createdAt = $createdAt;

//         return $this;
//     }

//     public function getUser(): ?User
//     {
//         return $this->user;
//     }

//     public function setUser(User $user): CommentModel
//     {
//         $this->user = $user;

//         return $this;
//     }

//     public function getPost(): ?Post
//     {
//         return $this->post;
//     }

//     public function setPost(Post $post): CommentModel
//     {
//         $this->post = $post;

//         return $this;
//     }

// }




// <?php

namespace App\Model;

class CommentModel
{
    private $id;
    private $content;
    private $userId;
    private $postId;
    private $createdAt;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

}
