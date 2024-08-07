<?php

namespace App\Interfaces;

interface CrudRepositoryInterface 
{
    public function list();
    public function getById(int $id);
    public function store(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function find(string $searchTerm);
}
