<?php

namespace App\Repository;

use PDO;
use App\Model\CategoryModel;

class CategoryRepository
{
  private PDO $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function save(CategoryModel $category)
  {
    $stmt = $this->db->prepare("INSERT INTO category (name) VALUES (:name)");
    $stmt->execute([
      'name' => $category->getName(),
    ]);
  }

  public function toArray(CategoryModel $category): array
  {
    return [
      'id' => $category->getId(),
      'name' => $category->getName(),
    ];
  }

  public function findOneById(int $categoryId): ?CategoryModel
  {
    $stmt = $this->db->prepare('SELECT * FROM category WHERE id = :id');
    $stmt->execute(['id' => $categoryId]);

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

  public function delete(int $categoryId)
  {
    $stmt = $this->db->prepare('DELETE FROM category WHERE id = :categoryId');
    $stmt->bindValue(':categoryId', $categoryId, \PDO::PARAM_INT);
    $stmt->execute();
  }
}
