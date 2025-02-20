<?php

    namespace App\Services;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use Illuminate\Support\Facades\Cache;
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
         * Caches the result for 1440 minutes (1 day).
         *
         * @param string $city
         * @param string $country ISO 3166-1 alpha-2 code (e.g., 'FR')
         * @return array|null Returns an array with 'latitude' and 'longitude', or null if not found.
         */
        public function getCoordinates(string $city, string $country): ?array
        {
            // Create a unique cache key based on city and country.
            $cacheKey = 'meteo_coordinates_' . md5($city . '_' . $country);
            $cacheTime = 1440; // in minutes (1 day)

            // Check if coordinates are cached.
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }

            $url = 'https://geocoding-api.open-meteo.com/v1/search';
            try {
                $params = [
                    'name'    => $city,
                    'country' => $country,
                ];

                $response = $this->client->request('GET', $url, [
                    'query' => $params,
                ]);

                $data = json_decode($response->getBody(), true);
                $results = $data['results'] ?? [];

                // Filter results by matching the country code (case-insensitive).
                $filteredResults = array_filter($results, function ($result) use ($country) {
                    return isset($result['country_code'])
                        && strtoupper($result['country_code']) === strtoupper($country);
                });

                if (!empty($filteredResults)) {
                    $firstResult = reset($filteredResults);
                    $coords = [
                        'latitude'  => $firstResult['latitude']  ?? null,
                        'longitude' => $firstResult['longitude'] ?? null,
                    ];
                } else {
                    // If no match, optionally fallback to the first result.
                    $firstResult = $results[0] ?? [];
                    $coords = [
                        'latitude'  => $firstResult['latitude']  ?? null,
                        'longitude' => $firstResult['longitude'] ?? null,
                    ];
                }

                // Only cache if coordinates were found.
                if ($coords['latitude'] && $coords['longitude']) {
                    Cache::put($cacheKey, $coords, $cacheTime * 60);
                    return $coords;
                }
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
                    return (float)$data['current_weather']['temperature'];
                }
            } catch (GuzzleException $e) {
                Log::error($e->getMessage());
            }

            return null;
        }
    }
