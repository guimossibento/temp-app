<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MeteoService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Get coordinates for a given city and country using the Open-Meteo Geocoding API.
     *
     * @param string $city
     * @param string $country ISO 3166-1 alpha-2 code (e.g., 'FR')
     * @return array|null Returns an array with 'latitude' and 'longitude', or null if not found.
     */
    public function getCoordinates(string $city, string $country): ?array
    {
        $url = 'https://geocoding-api.open-meteo.com/v1/search';

        try {
            $params = [
                'name'    => $city,
                'country' => $country,
            ];

            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            // Get the response body as a string and print it.
            $body = $response->getBody()->getContents();

            // Optionally decode JSON and print the array.
            $data = json_decode($body, true);

            $data = json_decode($response->getBody(), true);
            $results = $data['results'] ?? [];

            // Filter results by matching the country code (case-insensitive).
            $filteredResults = array_filter($results, function($result) use ($country) {
                return isset($result['country_code'])
                    && strtoupper($result['country_code']) === strtoupper($country);
            });

            if (!empty($filteredResults)) {
                // Get the first matching result.
                $firstResult = reset($filteredResults);
                return [
                    'latitude'  => $firstResult['latitude']  ?? null,
                    'longitude' => $firstResult['longitude'] ?? null,
                ];
            }

            // Fallback (if no result matches the country code, you might choose to return null or use the first result)
            $firstResult = $results[0] ?? [];
            return [
                'latitude'  => $firstResult['latitude']  ?? null,
                'longitude' => $firstResult['longitude'] ?? null,
            ];
        } catch (GuzzleException $e) {
           Log::error($e->getMessage());
        }

        return null;
    }

    /**
     * Get the current temperature for given coordinates using the Open-Meteo Forecast API.
     *
     * @param float $latitude
     * @param float $longitude
     * @return float|null Returns the current temperature or null if not available.
     */
    public function getCurrentTemperature(float $latitude, float $longitude): ?float
    {
        $url = 'https://api.open-meteo.com/v1/forecast';

        try {
            $response = $this->client->request('GET', $url, [
                'query' => [
                    'latitude'        => $latitude,
                    'longitude'       => $longitude,
                    'current_weather' => 'true',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['current_weather']) && isset($data['current_weather']['temperature'])) {
                return (float) $data['current_weather']['temperature'];
            }
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
