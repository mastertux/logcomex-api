<?php

namespace App\Repositories;

use App\Interfaces\CrudRepositoryInterface;
use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CityRepository implements CrudRepositoryInterface
{
    public function list(): LengthAwarePaginator
    {
        return City::paginate(2);
    }

    public function getById(int $id): City
    {
        return City::findOrFail($id);
    }

    public function store(array $data): City
    {
        return City::create($data);
    }

    public function update(array $data, int $id): void
    {
        City::whereId($id)->update($data);
    }

    public function delete(int $id): void
    {
        $city = $this->getById($id);
        $city->delete();
    }

    public function find(string $searchTerm): Collection
    {   
        $searchTerm = strtolower($searchTerm);

        return City::whereRaw("LOWER(city_name) = ? ", [$searchTerm])->get();
    }

    public function bulkInsert(array $cities): void
    {
        City::insert($cities);
    }
}
