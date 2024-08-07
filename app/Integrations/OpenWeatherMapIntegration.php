<?php

namespace App\Integrations;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PhpParser\Node\Expr\Throw_;

class OpenWeatherMapIntegration 
{
    const URL = 'http://api.openweathermap.org/geo/1.0/direct';

    public static function getCityInfo(string $cityName): array 
    {
        $client = app(Client::class);

        try {
            $response = $client->request('GET', self::URL, [
                'query' => [
                    'q' => $cityName,
                    'appid' => env('API_CITY_ID')
                ]
            ]);

            $cities = json_decode($response->getBody(), true);
            $now = Carbon::now();
            
            return collect($cities)->map(function($city) use ($now) {
                return [
                    'city_name' => $city['name'],
                    'state_code' => $city['state'] ?? null,
                    'country_code' => $city['country'],
                    'lat' => $city['lat'],
                    'lon' => $city['lon'],
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            })->toArray();
        } catch(\Exception $e) {
            throw $e; 
        }
    }
}