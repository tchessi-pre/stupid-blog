<?php

namespace App\Interface;

interface ServiceInterface
{
     public function create($data);
    // public function update($model);
    public function delete($model);
    public function getById($id);
    public function getAll();
    public function toArray($model);
}