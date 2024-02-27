<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\View\ViewRenderer;
use App\Class\Redirector;
use App\ErrorHandler\CategoryError\ErrorHandlerInterface;

class CategoryController
{
  private CategoryService $categoryService;
  private ViewRenderer $viewRenderer;
  private Redirector $redirector;
  private ErrorHandlerInterface $errorHandler;

  public function __construct(CategoryService $categoryService, ViewRenderer $viewRenderer, Redirector $redirector, ErrorHandlerInterface $errorHandler)
  {
    $this->categoryService = $categoryService;
    $this->viewRenderer = $viewRenderer;
    $this->redirector = $redirector;
    $this->errorHandler = $errorHandler;
  }

  public function create($request)
  {
    $error = $this->errorHandler->handleError($request);
    if ($error) {
      $this->redirector->redirect('category', ['error' => $error]);
      return;
    }

    $name = $request['name'];
    try {
      $this->categoryService->create($name);
      $this->redirector->redirect('category', ['success' => 'Category created successfully']);
    } catch (\Exception $e) {
      $this->redirector->redirect('category', ['error' => $e->getMessage()]);
    }
  }

  public function update($request)
  {
    $error = $this->errorHandler->handleError($request);
    if ($error) {
      $this->redirector->redirect('category', ['error' => $error]);
      return;
    }

    $categoryId = $request['id'] ?? null;
    $name = $request['name'] ?? '';

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
    // Utilisation du gestionnaire d'erreurs pour gérer les erreurs de redirection
    $error = $this->errorHandler->handleError($request);
    if ($error) {
      // Si une erreur de redirection est détectée, la redirection est déjà effectuée dans le gestionnaire d'erreurs
      return;
    }

    // Si aucune erreur de redirection n'est détectée, l'exécution continue normalement
    // Gestion des autres erreurs de manière similaire à ce que vous avez déjà implémenté
    $categoryId = $request['id'] ?? null;

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
