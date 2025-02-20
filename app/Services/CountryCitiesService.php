<?php

    namespace App\Services;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Log;

    class CountryCitiesService
    {
        protected Client $client;

        public function __construct()
        {
            $this->client = new Client();
        }

        /**
         * Retrieve a list of countries (with their cities) from the external API.
         * The results are cached for 1440 minutes (1 day).
         *
         * Each item should contain:
         *  - 'country': The country name.
         *  - 'iso2': The ISO 3166-1 alpha-2 code.
         *  - 'cities': An array of city names.
         *
         * @return array
         */
        public function getCountriesData(): array
        {
            return Cache::remember('countries_cities', 1440, function () {
                $url = 'https://countriesnow.space/api/v0.1/countries';
                try {
                    $response = $this->client->request('GET', $url);
                    $data = json_decode($response->getBody(), true);
                    if (isset($data['data']) && is_array($data['data'])) {
                        return $data['data'];
                    }
                } catch (GuzzleException $e) {
                    Log::error($e->getMessage());
                }
                return [];
            });
        }

        /**
         * Get a list of countries with both ISO2 code and name.
         *
         * Returns an array of associative arrays:
         * [
         *    [ "code" => "FR", "name" => "France" ],
         *    [ "code" => "US", "name" => "United States" ],
         *    ...
         * ]
         *
         * @return array
         */
        public function getCountries(): array
        {
            $countriesData = $this->getCountriesData();
            $countries = [];
            foreach ($countriesData as $item) {
                if (isset($item['country']) && isset($item['iso2'])) {
                    $countries[] = [
                        'code' => strtoupper($item['iso2']),
                        'name' => $item['country']
                    ];
                }
            }
            // Sort alphabetically by country name
            usort($countries, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            return $countries;
        }

        /**
         * Get a list of cities for a given country.
         *
         * @param string $countryCode The country ISO2 code (case-insensitive).
         * @return array Returns an array of city names.
         */
        public function getCities(string $countryCode): array
        {
            $countriesData = $this->getCountriesData();
            foreach ($countriesData as $item) {
                if (isset($item['iso2']) && strcasecmp($item['iso2'], $countryCode) === 0) {
                    return $item['cities'] ?? [];
                }
            }
            return [];
        }
    }
