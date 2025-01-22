<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        $this->baseUrl = config('services.openweather.url');
    }

    public function getCurrentWeather(string $city, string $units)
    {
        $cacheKey = "weather.current.{$city}.{$units}";
        
        return Cache::remember($cacheKey, 1800, function () use ($city, $units) {
            $response = Http::get("{$this->baseUrl}/weather", [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => $units ?? 'metric'
            ]);
            
            $weatherData = $response->json();

            if ($units === 'imperial') {
                $weatherData = $this->convertToImperial($weatherData);
            }

            return $weatherData;
        });
    }

    public function getForecast(string $city, string $units)
    {
        $cacheKey = "weather.forecast.{$city}.{$units}";
        
        return Cache::remember($cacheKey, 1800, function () use ($city, $units) {
            $response = Http::get("{$this->baseUrl}/forecast", [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => $units ?? 'metric'
            ]);
            
            $forecastData = $response->json();

            if ($units === 'imperial') {
                $forecastData = $this->convertToImperial($forecastData);
            }

            return $forecastData;
        });
    }

    private function convertToImperial($weatherData)
    {
        if (isset($weatherData['main']['temp'])) {
            $weatherData['main']['temp'] = $this->celsiusToFahrenheit($weatherData['main']['temp']);
        }
        if (isset($weatherData['wind']['speed'])) {
            $weatherData['wind']['speed'] = $this->metersPerSecondToMilesPerHour($weatherData['wind']['speed']);
        }
        // Add other conversions as needed

        return $weatherData;
    }

    private function celsiusToFahrenheit($celsius)
    {
        return ($celsius * 9/5) + 32;
    }

    private function metersPerSecondToMilesPerHour($metersPerSecond)
    {
        return $metersPerSecond * 2.23694;
    }

    // Add other conversion methods as needed
}