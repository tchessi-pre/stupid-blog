<?php

namespace App\ErrorHandler\CategoryError;

class DeleteErrorHandler extends AbstractErrorHandler
{
  public function handleError($request)
  {
    $categoryId = $request['id'] ?? null;
    if (is_null($categoryId)) {
      return 'Invalid category ID';
    } elseif ($this->nextHandler) {
      return $this->nextHandler->handleError($request);
    } else {
      return null;
    }
  }
}