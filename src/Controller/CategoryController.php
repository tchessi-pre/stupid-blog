<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\View\ViewRenderer;
use App\Class\Redirector;
use App\Interface\ControllerInterface;

class CategoryController implements ControllerInterface
{
  private CategoryService $categoryService;
  private ViewRenderer $viewRenderer;
  private Redirector $redirector;

  public function __construct(CategoryService $categoryService, ViewRenderer $viewRenderer, Redirector $redirector)
  {
    $this->categoryService = $categoryService;
    $this->viewRenderer = $viewRenderer;
    $this->redirector = $redirector;
  }

  public function create($request)
  {
    $name = $request['name'] ?? '';

    if (empty($name)) {
      $this->redirector->redirect('category', ['error' => 'Category name cannot be empty']);
      return;
    }

    try {
      $this->categoryService->create($name);
      $this->redirector->redirect('category', ['success' => 'Category created successfully']);
    } catch (\Exception $e) {
      $this->redirector->redirect('category', ['error' => $e->getMessage()]);
    }
  }

  public function update($request)
  {
    $categoryId = $request['id'] ?? null;
    $name = $request['name'] ?? '';

    if (is_null($categoryId) || empty($name)) {
      $this->redirector->redirect('category', ['error' => 'Invalid category data']);
      return;
    }

    try {
      $category = $this->categoryService->getById($categoryId);
      if (!$category) {
        $this->redirector->redirect('category', ['error' => 'Category not found']);
        return;
      }

      $category->setName($name);
      $this->categoryService->update($category);
      $this->redirector->redirect('category', ['success' => 'Category updated successfully']);
    } catch (\Exception $e) {
      $this->redirector->redirect('category', ['error' => $e->getMessage()]);
    }
  }

  public function delete($request)
  {
    $categoryId = $request['id'] ?? null;

    if (is_null($categoryId)) {
      $this->redirector->redirect('category', ['error' => 'Invalid category ID']);
      return;
    }

    try {
      $category = $this->categoryService->getById($categoryId);
      if (!$category) {
        $this->redirector->redirect('category', ['error' => 'Category not found']);
        return;
      }

      $this->categoryService->delete($category);
      $this->redirector->redirect('category', ['success' => 'Category deleted successfully']);
    } catch (\Exception $e) {
      $this->redirector->redirect('category', ['error' => $e->getMessage()]);
    }
  }
}
