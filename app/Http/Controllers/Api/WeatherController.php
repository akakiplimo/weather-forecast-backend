<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    private WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function current(Request $request): JsonResponse
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'units' => 'nullable|string|in:standard,metric,imperial'
        ]);

        try {
            $weather = $this->weatherService->getCurrentWeather(
                $request->city,
                $request->units ?? 'metric'
            );
            $weather = $this->convertUnits($weather, $request->units ?? 'metric');
            return response()->json($weather);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch weather data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function forecast(Request $request): JsonResponse
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'units' => 'nullable|string|in:standard,metric,imperial'
        ]);

        try {
            $forecast = $this->weatherService->getForecast(
                $request->city,
                $request->units ?? 'metric'
            );
            $forecast = $this->convertUnits($forecast, $request->units ?? 'metric');
            return response()->json($forecast);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch forecast data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function convertUnits(array $data, string $units): array
    {
        // Implement unit conversion logic here
        return $data;
    }
}