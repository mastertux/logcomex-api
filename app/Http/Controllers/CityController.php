<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Http\Requests\FindCity;
use App\Services\CityService;
use App\Utils\ApiResponseClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CityController extends Controller
{   
    private CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }
    
    public function list(): JsonResponse
    {
        $cities = $this->cityService->list();

        return ApiResponseClass::sendResponse($cities, '', 200);
    }

    public function show(int $id): JsonResponse
    {
        $city = $this->cityService->show($id);

        return ApiResponseClass::sendResponse(new CityResource($city), '', 200);
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = [
            'city_name' => $request->city_name,
            'state_code' => $request->state_code,
            'country_code' => $request->country_code,
            'lat' => $request->lat,
            'lon' => $request->lon
        ];

        try{
            $city = $this->cityService->store($city);

            return ApiResponseClass::sendResponse(new CityResource($city), 'City created successful', 201);
        }catch(\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    public function update(UpdateCityRequest $request, int $id): Response
    {
        $city = [
            'city_name' => $request->city_name,
            'state_code' => $request->state_code,
            'country_code' => $request->country_code,
            'lat' => $request->lat,
            'lon' => $request->lon
        ];

        try {
            $this->cityService->update($city, $id);

            return response()->noContent(201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }
    
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->cityService->destroy($id);

            return ApiResponseClass::sendResponse('City deleted successfull', '', 204);
        } catch (\Exception $e) {
            return response()->noContent(404);
        }
    }

    public function find(FindCity $findCity): JsonResponse|Response
    {
        $cities = $this->cityService->find($findCity->city_name);

        if (!$cities) {
            return response()->noContent(404);
        }

        return ApiResponseClass::sendResponse($cities, '', 200);
    }
}
