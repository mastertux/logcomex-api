<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery;
use App\Services\CityService;
use App\Models\City;
use App\Repositories\CityRepository;
use App\Integrations\OpenWeatherMapIntegration;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CityServiceTest extends TestCase
{
    public function testList()
    {
        $cityRepositoryMock = $this->createMock(CityRepository::class);
        $openWeatherMapIntegrationMock = $this->createMock(OpenWeatherMapIntegration::class);

        $sampleData = collect(['city1', 'city2', 'city3']);
        $paginator = new LengthAwarePaginator($sampleData, $sampleData->count(), 10, 1);

        $cityRepositoryMock->method('list')
            ->willReturn($paginator);

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);

        $result = $cityService->list();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);

        $this->assertEquals($paginator, $result);
    }

    public function testShowReturnsCity()
    {
        $cityRepositoryMock =  Mockery::mock(CityRepository::class);
        $openWeatherMapIntegrationMock = $this->createMock(OpenWeatherMapIntegration::class);

        $city = new City();
        $city->id = 1;
        $city->name = 'Sample City';
        $city->state_code = 'Minas Gerais';
        $city->country_code = 'BR';

        $cityRepositoryMock
            ->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($city);

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);

        $result = $cityService->show(1);

        $this->assertInstanceOf(City::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Sample City', $result->name);
    }

    public function testStoreCreatesAndReturnsCity()
    {
        $cityRepositoryMock = Mockery::mock(CityRepository::class);
        $openWeatherMapIntegrationMock = $this->createMock(OpenWeatherMapIntegration::class);

        $inputData = [
            'city_name' => 'New City',
            'state_code' => 'NY',
            'country_code' => 'US',
            'lat' => 40.7128,
            'lon' => -74.0060,
        ];

        $city = new City();
        $city->id = 1;
        $city->city_name = 'New City';
        $city->state_code = 'NY';
        $city->country_code = 'US';
        $city->lat = 40.7128;
        $city->lon = -74.0060;
        

        $cityRepositoryMock
            ->shouldReceive('store')
            ->with($inputData)
            ->once()
            ->andReturn($city);

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);

        $result = $cityService->store($inputData);

        $this->assertInstanceOf(City::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('New City', $result->city_name);
        $this->assertEquals('NY', $result->state_code);
        $this->assertEquals('US', $result->country_code);
        $this->assertEquals(40.7128, $result->lat);
        $this->assertEquals(-74.0060, $result->lon);
    }

    public function testUpdateCallsRepositoryWithCorrectParameters()
    {
        $cityData = [
            'city_name' => 'Updated City',
            'state_code' => 'CA',
            'country_code' => 'US',
            'lat' => 34.0522,
            'lon' => -118.2437,
        ];
        $cityId = 1;

        $cityRepositoryMock = Mockery::mock(CityRepository::class);
        $openWeatherMapIntegrationMock = $this->createMock(OpenWeatherMapIntegration::class);
        $cityRepositoryMock->shouldReceive('update')
                           ->with($cityData, $cityId)
                           ->once();

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);
        $cityService->update($cityData, $cityId);

        $cityRepositoryMock->shouldHaveReceived('update')
                           ->with($cityData, $cityId)
                           ->once();

         $this->assertTrue(Mockery::getContainer()->mockery_getExpectationCount() > 0);
    }

    public function testDestroyCallsRepositoryWithCorrectParameter()
    {
        $cityId = 1;

        $cityRepositoryMock = Mockery::mock(CityRepository::class);
        $openWeatherMapIntegrationMock = $this->createMock(OpenWeatherMapIntegration::class);
        $cityRepositoryMock->shouldReceive('delete')
                           ->with($cityId)
                           ->once();

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);
        $cityService->destroy($cityId);

        $cityRepositoryMock->shouldHaveReceived('delete')
                           ->with($cityId)
                           ->once();
        
        $this->assertTrue(Mockery::getContainer()->mockery_getExpectationCount() > 0);
    }

    public function testFindReturnsCityFromRepositoryIfExists()
    {
        $cityName = "SampleCity";
        $cityData = ['name' => $cityName, 'population' => 500000];

        $cityRepositoryMock = Mockery::mock(CityRepository::class);
        $cityRepositoryMock->shouldReceive('find')
                           ->with($cityName)
                           ->andReturn(new Collection([$cityData]));

        $openWeatherMapIntegrationMock = $this->createMock(OpenWeatherMapIntegration::class);

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);

        $result = $cityService->find($cityName);

        $this->assertEquals([$cityData], $result);
    }

    public function testFindFetchesFromApiAndInsertsWhenCityIsNotFoundInRepository()
    {
        $cityName = "SampleCity";
        $apiCities = [
            [
                'city_name' => $cityName,
                'state_code' => 'SampleState',
                'country_code' => 'SampleCountry',
                'lat' => 40.7128,
                'lon' => -74.0060
            ]
        ];

        $cityRepositoryMock = Mockery::mock(CityRepository::class);
        $cityRepositoryMock->shouldReceive('find')
                        ->with($cityName)
                        ->andReturn(new Collection())
                        ->once()
                        ->ordered()
                        ->shouldReceive('bulkInsert')
                        ->with($apiCities)
                        ->once()
                        ->ordered()
                        ->shouldReceive('find')
                        ->with($cityName)
                        ->andReturn(new Collection($apiCities))
                        ->once()
                        ->ordered();

        $openWeatherMapIntegrationMock = Mockery::mock(OpenWeatherMapIntegration::class);
        $openWeatherMapIntegrationMock->shouldReceive('getCityInfo')
                                    ->with($cityName)
                                    ->andReturn($apiCities);

        $cityService = new CityService($cityRepositoryMock, $openWeatherMapIntegrationMock);

        $result = $cityService->find($cityName);

        $this->assertEquals($apiCities, $result);
    }
}
