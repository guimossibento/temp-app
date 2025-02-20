<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\Temperature;
    use Illuminate\Http\Request;
    use Spatie\QueryBuilder\AllowedFilter;
    use Spatie\QueryBuilder\QueryBuilder;

    class TemperatureController extends Controller
    {
        /**
         * Display a listing of the temperature records.
         */
        public function index()
        {
            $temperatures = QueryBuilder::for(Temperature::class)
                ->allowedFilters([
                    AllowedFilter::exact('city_id'),
                    'unit'
                ])
                ->with('city')
                ->get();

            return response()->json(['data' => $temperatures], 200);
        }

        /**
         * Store a newly created temperature record in storage.
         */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'city_id' => 'required|exists:cities,id',
                'value' => 'required|numeric',
                'unit' => 'required|string|in:C,F',
                'recorded_at' => 'required|date',
            ]);

            $temperature = Temperature::create($validated);

            return response()->json(['data' => $temperature], 201);
        }

        /**
         * Display the specified temperature record.
         */
        public function show(Temperature $temperature)
        {
            return response()->json(['data' => $temperature->load('city')], 200);
        }

        /**
         * Update the specified temperature record in storage.
         */
        public function update(Request $request, Temperature $temperature)
        {
            $validated = $request->validate([
                'city_id' => 'required|exists:cities,id',
                'value' => 'required|numeric',
                'unit' => 'required|string|in:C,F',
                'recorded_at' => 'required|date',
            ]);

            $temperature->update($validated);

            return response()->json(['data' => $temperature], 200);
        }

        /**
         * Remove the specified temperature record from storage.
         */
        public function destroy(Temperature $temperature)
        {
            $temperature->delete();

            return response()->json(null, 204);
        }
    }
