<?php

namespace App\ErrorHandler\Category;

use App\ErrorHandler\Category\ErrorHandlerInterface;

class EmptyCategoryIdOrNameErrorHandler implements ErrorHandlerInterface
{
  public function handleError($request): ?string
  {
    $categoryId = $request['id'] ?? null;
    $name = $request['name'] ?? '';

    if (is_null($categoryId) || empty($name)) {
      return 'Invalid category data';
    }
    return null;
  }
}