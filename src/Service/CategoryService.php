<?php

namespace App\Service;

use App\Interface\ServiceInterface;
use App\Repository\CategoryRepository;
use App\Model\CategoryModel;

class CategoryService implements ServiceInterface
{
  private CategoryRepository $categoryRepository;

  public function __construct(CategoryRepository $categoryRepository)
  {
    $this->categoryRepository = $categoryRepository;
  }

  public function create($data): CategoryModel
  {
    $name = $data['name'] ?? null;
    
    $category = new CategoryModel();
    $category->setName($name);
    $this->categoryRepository->save($category);
    return $category;
  }

  public function update($category): void
  {
    $this->categoryRepository->save($category);
  }

  public function delete($category): void
  {
    $this->categoryRepository->delete($category->getId());
  }

  public function getById($id): ?CategoryModel
  {
    return $this->categoryRepository->findOneById($id);
  }

  public function getAll(): array
  {
    return $this->categoryRepository->findAll();
  }

  public function toArray($category): array
  {
    return [
      'id' => $category->getId(),
      'name' => $category->getName(),
    ];
  }
}
