<?php

namespace App\Class;

class Category
{

    public function __construct(
        private ?int $id = null,
        private ?string $name = null,
        private ?array $posts = []
    ) {
    }

    /**
     * Get the value of id
     * 
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param ?int $id
     * @return  self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     * 
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param ?string $name
     * @return  self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of posts
     * 
     * @return Post[]
     */
    public function getPosts(): array
    {
        return $this->posts;
    }

    /**
     * Set the value of posts
     *
     * @param Post[] $posts 
     * @return  self
     */
    public function setPosts(array $posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'posts' => count($this->posts)
        ];
    }

    public function findOneById(int $id)
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('SELECT * FROM category WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);
        $category = $query->fetch(\PDO::FETCH_ASSOC);
        $this->setId($category['id'])
            ->setName($category['name']);

        return $this;
    }

    public function findAll()
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('SELECT * FROM category');
        $query->execute();
        $categories = $query->fetchAll(\PDO::FETCH_ASSOC);
        $results = [];
        foreach ($categories as $category) {
            $results[] = (new Category())
                ->setId($category['id'])
                ->setName($category['name']);
        }

        return $results;
    }


    /**
     * Save Post in database
     *
     * @return self
     */
    public function save(): self
    {
        if (is_null($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }

        return $this;
    }

    /**
     * Insert post in database
     *
     * @return self
     */
    private function insert(): self
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('INSERT INTO category (name) VALUES (:name)');
        $query->execute([
            'name' => $this->name
        ]);
        $this->id = $pdo->lastInsertId();

        return $this;
    }

    /**
     * Udpdate post in database
     *
     * @return self
     */
    private function update(): self
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('UPDATE category SET name = :name WHERE id = :id');
        $query->execute([
            'name' => $this->name,
            'id' => $this->id
        ]);

        return $this;
    }

    public function delete()
    {
        $pdo = Database::getConnection();
        $query = $pdo->prepare('DELETE FROM category WHERE id = :id');
        $query->bindValue(':id', $this->id, \PDO::PARAM_INT);
        $query->execute();
    }
}
