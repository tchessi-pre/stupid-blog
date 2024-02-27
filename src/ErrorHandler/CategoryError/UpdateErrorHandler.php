<?php

namespace App\ErrorHandler\CategoryError;

class UpdateErrorHandler extends AbstractErrorHandler
{
  public function handleError($request)
  {
    $categoryId = $request['id'] ?? null;
    $name = $request['name'] ?? '';

    if (is_null($categoryId) || empty($name)) {
      return 'Invalid category data';
    } elseif ($this->nextHandler) {
      return $this->nextHandler->handleError($request);
    } else {
      return null;
    }
  }
}