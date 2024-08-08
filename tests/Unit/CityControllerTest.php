<?php

namespace Tests\Unit;

use App\Http\Controllers\CityController;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Requests\FindCity;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $cityServiceMock;
    protected $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->cityServiceMock = Mockery::mock(CityService::class);
        $this->controller = new CityController($this->cityServiceMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testList()
    {
        $cities = City::factory()->count(3)->make();
        $paginator = new LengthAwarePaginator($cities, 3, 10);

        $this->cityServiceMock
            ->shouldReceive('list')
            ->once()
            ->andReturn($paginator);

        $response = $this->controller->list();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShow()
    {
        $city = City::factory()->make(['id' => 1]);
        $this->cityServiceMock
            ->shouldReceive('show')
            ->with(1)
            ->once()
            ->andReturn($city);

        $response = $this->controller->show(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $requestData = [
            'city_name' => 'Sample City',
            'state_code' => 'SC',
            'country_code' => 'US',
            'lat' => 12.345678,
            'lon' => 98.765432
        ];

        $storeRequest = StoreCityRequest::create('/cities', 'POST', $requestData);

        $storedCity = City::factory()->make($requestData);

        $this->cityServiceMock
            ->shouldReceive('store')
            ->with(Mockery::subset($requestData))
            ->once()
            ->andReturn($storedCity);

        $response = $this->controller->store($storeRequest);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $requestData = [
            'city_name' => 'Updated City',
            'state_code' => 'UC',
            'country_code' => 'US',
            'lat' => 22.345678,
            'lon' => 88.765432
        ];

        $updateRequest = UpdateCityRequest::create('/cities/1', 'PUT', $requestData);

        $this->cityServiceMock
            ->shouldReceive('update')
            ->with(Mockery::subset($requestData), 1)
            ->once();

        $response = $this->controller->update($updateRequest, 1);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testDestroy()
    {
        $this->cityServiceMock
            ->shouldReceive('destroy')
            ->with(1)
            ->once();

        $response = $this->controller->destroy(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testFind()
    {
        $findRequest = new FindCity();
        $findRequest->merge(['city_name' => 'Sample']);

        $citiesArray = City::factory()->count(3)->make(['city_name' => 'Sample'])->toArray();

        $this->cityServiceMock
            ->shouldReceive('find')
            ->with('Sample')
            ->once()
            ->andReturn($citiesArray);

        $response = $this->controller->find($findRequest);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    
}
