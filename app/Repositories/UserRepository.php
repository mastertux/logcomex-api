<?php

namespace App\Repositories;
use App\Interfaces\CrudRepositoryInterface;
use App\Models\User;

class UserRepository implements CrudRepositoryInterface
{
    public function list()
    {
        return User::paginate(2);
    }

    public function getById($id)
    {
        return User::findOrFail($id);
    }

    public function store(array $data)
    {
        return User::create($data);
    }

    public function update(array $data, $id)
    {
        return User::whereId($id)->update($data);
    }

    public function delete($id)
    {
        User::destroy($id);
    }

    public function find($searchTerm)
    {    
        return User::where('name', $searchTerm)->get();   
    }
}
