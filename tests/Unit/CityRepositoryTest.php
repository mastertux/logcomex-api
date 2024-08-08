<?php

namespace Tests\Unit;

use App\Models\City;
use App\Repositories\CityRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cityRepository = new CityRepository();
    }

    public function testListReturnsPaginatedResults()
    {
        City::factory()->count(5)->create();

        $result = $this->cityRepository->list();

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->perPage());
        $this->assertCount(2, $result);
    }

    public function testGetByIdReturnsCity()
    {
        $city = City::factory()->create();

        $result = $this->cityRepository->getById($city->id);

        $this->assertInstanceOf(City::class, $result);
        $this->assertEquals($city->id, $result->id);
    }

    public function testGetByIdThrowsExceptionIfCityNotFound()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->cityRepository->getById(9999);
    }

    public function testStoreCreatesNewCity()
    {
        $cityData = [
            'city_name' => 'New City',
            'state_code' => 'NC',
            'country_code' => 'US',
            'lat' => 40.7128,
            'lon' => -74.0060
        ];

        $result = $this->cityRepository->store($cityData);

        $this->assertInstanceOf(City::class, $result);
        $this->assertDatabaseHas('cities', ['city_name' => 'New City']);
    }

    public function testUpdateModifiesExistingCity()
    {
        $city = City::factory()->create([
            'city_name' => 'Old City'
        ]);

        $updateData = [
            'city_name' => 'Updated City'
        ];

        $this->cityRepository->update($updateData, $city->id);

        $this->assertDatabaseHas('cities', ['city_name' => 'Updated City']);
        $this->assertDatabaseMissing('cities', ['city_name' => 'Old City']);
    }

    public function testDeleteRemovesCity()
    {
        $city = City::factory()->create();

        $this->cityRepository->delete($city->id);

        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }

    public function testFindReturnsCitiesMatchingSearchTerm()
    {
        City::factory()->create([
            'city_name' => 'SampleCity'
        ]);
        City::factory()->create([
            'city_name' => 'AnotherCity'
        ]);

        $result = $this->cityRepository->find('samplecity');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals('SampleCity', $result->first()->city_name);
    }

    public function testBulkInsertInsertsMultipleCities()
    {
        $citiesData = [
            [
                'city_name' => 'CityOne',
                'state_code' => 'ST1',
                'country_code' => 'C1',
                'lat' => 10.0000,
                'lon' => 20.0000
            ],
            [
                'city_name' => 'CityTwo',
                'state_code' => 'ST2',
                'country_code' => 'C2',
                'lat' => 30.0000,
                'lon' => 40.0000
            ]
        ];

        $this->cityRepository->bulkInsert($citiesData);

        $this->assertDatabaseHas('cities', ['city_name' => 'CityOne']);
        $this->assertDatabaseHas('cities', ['city_name' => 'CityTwo']);
    }
}
