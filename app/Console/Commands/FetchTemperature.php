<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Temperature;
use App\Services\MeteoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchTemperature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:temperature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching temperature for all cities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Resolve the MeteoService from the container.
        $meteoService = app(MeteoService::class);

        // Retrieve all cities.
        $cities = City::all();

        foreach ($cities as $city) {
            // Check if the city has valid coordinates.
            if (!$city->latitude || !$city->longitude) {
                Log::warning("City '{$city->name}' (ID: {$city->id}) missing coordinates. Skipping temperature fetch.");
                continue;
            }

            // Fetch the current temperature.
            $temperatureValue = $meteoService->getCurrentTemperature($city->latitude, $city->longitude);

            if ($temperatureValue !== null) {
                // Create a new temperature record.
                Temperature::create([
                    'city_id'     => $city->id,
                    'value'       => $temperatureValue,
                    'unit'        => 'C',
                    'recorded_at' => now(),
                ]);
                Log::info("Recorded temperature {$temperatureValue}°C for city '{$city->name}' (ID: {$city->id}).");
            } else {
                Log::warning("No temperature data available for city '{$city->name}' (ID: {$city->id}).");
            }
        }
    }
}
