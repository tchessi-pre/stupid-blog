<?php

namespace App\ErrorHandler\CategoryError;

use App\Service\CategoryService;

class CategoryNotFoundErrorHandler extends AbstractErrorHandler
{
  private $categoryService;

  public function __construct(CategoryService $categoryService)
  {
    $this->categoryService = $categoryService;
  }

  public function handleError($request)
  {
    $categoryId = $request['id'] ?? null;
    if (!$this->categoryService->getById($categoryId)) {
      return 'Category not found';
    } elseif ($this->nextHandler) {
      return $this->nextHandler->handleError($request);
    } else {
      return null;
    }
  }
}
