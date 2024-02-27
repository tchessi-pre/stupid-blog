<?php

namespace App\ErrorHandler\CategoryError;

abstract class AbstractErrorHandler implements ErrorHandlerInterface
{
  protected $nextHandler;

  public function setNext(ErrorHandlerInterface $handler): ErrorHandlerInterface
  {
    $this->nextHandler = $handler;
    return $handler;
  }
}