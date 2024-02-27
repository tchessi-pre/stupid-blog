<?php

namespace App\ErrorHandler\CategoryError;

use App\Service\CategoryService;
use App\Class\Redirector;

class RedirectErrorHandler extends AbstractErrorHandler
{
  private CategoryService $categoryService;
  private Redirector $redirector;

  public function __construct(CategoryService $categoryService, Redirector $redirector)
  {
    $this->categoryService = $categoryService;
    $this->redirector = $redirector;
  }

  public function handleError($request)
  {
    try {
      // Tentative d'exécution de la logique de suppression
      $categoryId = $request['id'] ?? null;
      $category = $this->categoryService->getById($categoryId);

      if (!$category) {
        return 'Category not found';
      }

      $this->categoryService->delete($category);
      $this->redirector->redirect('category', ['success' => 'Category deleted successfully']);
    } catch (\Exception $e) {
      // Si une exception est levée pendant la suppression, rediriger avec l'erreur
      $this->redirector->redirect('category', ['error' => $e->getMessage()]);
    }
  }
}
