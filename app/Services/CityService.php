<?php

namespace App\Services;

use App\Integrations\OpenWeatherMapIntegration;
use App\Models\City;
use App\Repositories\CityRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CityService 
{
    private CityRepository $cityRepository;
    private OpenWeatherMapIntegration $openWeatherMapIntegration;

    public function __construct(CityRepository $cityRepository, OpenWeatherMapIntegration $openWeatherMapIntegration)
    {
        $this->cityRepository = $cityRepository;
        $this->openWeatherMapIntegration = $openWeatherMapIntegration;    
    }

    public function list(): LengthAwarePaginator
    {
        return $this->cityRepository->list();
    }

    public function show(int $id): City
    {
        return $this->cityRepository->getById($id);
    }

    public function store(array $city): City
    {
        return $this->cityRepository->store($city);
    }

    public function update(array $city, $id): void
    {
        $this->cityRepository->update($city, $id);
    }

    public function destroy(int $id): void
    {
        $this->cityRepository->delete($id);
    }

    public function find(string $cityName): ?array
    {
        $city = $this->cityRepository->find($cityName);

        if($city->isNotEmpty()) {
            return $city->toArray();
        }

        $cities = $this->openWeatherMapIntegration::getCityInfo($cityName);

        if (count($cities) === 0) {
            return null;
        }

        $this->cityRepository->bulkInsert($cities);

        return $this->find($cityName);
    }
}
