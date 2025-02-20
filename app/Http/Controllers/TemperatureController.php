<?php

    namespace App\Http\Controllers;

    use App\Models\Temperature;
    use App\Models\City;
    use Illuminate\Http\Request;

    class TemperatureController extends Controller
    {
        /**
         * Display a listing of the temperature records.
         */
        public function index()
        {
            // Eager load the related city
            $temperatures = Temperature::with('city')
                ->orderBy('recorded_at', 'desc')
                ->paginate(15);
            return view('temperatures.index', compact('temperatures'));
        }

        /**
         * Show the form for creating a new temperature record.
         */
        public function create()
        {
            // Get all cities to populate a dropdown selection
            $cities = City::orderBy('name')->get();
            return view('temperatures.create', compact('cities'));
        }

        /**
         * Store a newly created temperature record in storage.
         */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'city_id'     => 'required|exists:cities,id',
                'value'       => 'required|numeric',
                'unit'        => 'required|string|in:C,F',
                'recorded_at' => 'required|date',
            ]);

            Temperature::create($validated);

            return redirect()->route('temperatures.index')
                ->with('success', 'Temperature record created successfully.');
        }

        /**
         * Show the form for editing the specified temperature record.
         */
        public function edit(Temperature $temperature)
        {
            $cities = City::orderBy('name')->get();
            return view('temperatures.edit', compact('temperature', 'cities'));
        }

        /**
         * Update the specified temperature record in storage.
         */
        public function update(Request $request, Temperature $temperature): \Illuminate\Http\RedirectResponse
        {
            $validated = $request->validate([
                'city_id'     => 'required|exists:cities,id',
                'value'       => 'required|numeric',
                'unit'        => 'required|string|in:C,F',
                'recorded_at' => 'required|date',
            ]);

            $temperature->update($validated);

            return redirect()->route('temperatures.index')
                ->with('success', 'Temperature record updated successfully.');
        }

        /**
         * Remove the specified temperature record from storage.
         */
        public function destroy(Temperature $temperature): \Illuminate\Http\RedirectResponse
        {
            $temperature->delete();

            return redirect()->route('temperatures.index')
                ->with('success', 'Temperature record deleted successfully.');
        }
    }
