<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Model\CategoryModel;

class CategoryService
{
  private CategoryRepository $categoryRepository;

  public function __construct(CategoryRepository $categoryRepository)
  {
    $this->categoryRepository = $categoryRepository;
  }

  public function createCategory(string $name): CategoryModel
  {
    $category = new CategoryModel();
    $category->setName($name);
    $this->categoryRepository->save($category);
    return $category;
  }

  public function updateCategory(CategoryModel $category): void
  {
    $this->categoryRepository->save($category);
  }

  public function deleteCategory(CategoryModel $category): void
  {
    $this->categoryRepository->delete($category->getId());
  }

  public function getCategoryById(int $id): ?CategoryModel
  {
    return $this->categoryRepository->findOneById($id);
  }

  public function getAllCategories(): array
  {
    return $this->categoryRepository->findAll();
  }

  public function categoryToArray(CategoryModel $category): array
  {
    return [
      'id' => $category->getId(),
      'name' => $category->getName(),
    ];
  }
}
