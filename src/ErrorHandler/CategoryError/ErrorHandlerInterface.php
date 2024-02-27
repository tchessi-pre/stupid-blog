<?php

//  Cette interface définit la méthode handleError, 
//que chaque gestionnaire d'erreurs devra implémenter

namespace App\ErrorHandler\CategoryError;

interface ErrorHandlerInterface
{
  public function setNext(ErrorHandlerInterface $handler): ErrorHandlerInterface;
  public function handleError($request);
}