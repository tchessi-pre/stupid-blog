<?php

// Implémentation des gestionnaires d'erreurs

namespace App\ErrorHandler\CategoryError;

class EmptyNameErrorHandler extends AbstractErrorHandler
{
  public function handleError($request)
  {
    $name = $request['name'] ?? '';
    if (empty($name)) {
      return 'Category name cannot be empty';
    } elseif ($this->nextHandler) {
      return $this->nextHandler->handleError($request);
    } else {
      return null;
    }
  }
}

