<?php

namespace App\Interface;

interface ControllerInterface
{
  public function create($request);
  public function update($request);
  public function delete($request);
}
