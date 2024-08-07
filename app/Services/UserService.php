<?php

namespace App\Services;
use App\Repositories\UserRepository;

class UserService 
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function list()
    {
        return $this->userRepository->list();
    }

    public function show(int $id)
    {
        return $this->userRepository->getById($id);
    }

    public function store(array $user)
    {
        return $this->userRepository->store($user);
    }

    public function update(array $user, $id)
    {
        return $this->userRepository->update($user, $id);
    }

    public function destroy(int $id)
    {
        return $this->userRepository->delete($id);
    }

    public function find(string $user) 
    {
        return $this->userRepository->find($user);
    }
}