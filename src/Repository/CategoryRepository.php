<?php

namespace App\Repository;

use PDO;
use App\Model\CategoryModel;
use App\Interface\RepositoryInterface;

class CategoryRepository implements RepositoryInterface
{
  private PDO $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function save($model)
  {
    $stmt = $this->db->prepare("INSERT INTO category (name) VALUES (:name)");
    $stmt->execute([
      'name' => $model->getName(),
    ]);
  }

  public function findOneById(int $id)
  {
    $stmt = $this->db->prepare('SELECT * FROM category WHERE id = :id');
    $stmt->execute(['id' => $id]);

    $categoryData = $stmt->fetch();

    if (!$categoryData) {
      return null;
    }

    $category = new CategoryModel();
    $category->setId($categoryData['id']);
    $category->setName($categoryData['name']);

    return $category;
  }

  public function findAll(): array
  {
    $stmt = $this->db->prepare('SELECT * FROM category');
    $stmt->execute();

    $categories = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $categoryData) {
      $category = new CategoryModel();
      $category->setId($categoryData['id']);
      $category->setName($categoryData['name']);

      $categories[] = $category;
    }

    return $categories;
  }

  public function delete(int $id)
  {
    $stmt = $this->db->prepare('DELETE FROM category WHERE id = :id');
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->execute();
  }
}
