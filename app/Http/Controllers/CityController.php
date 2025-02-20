<?php

    namespace App\Http\Controllers;

    use App\Models\City;
    use App\Services\MeteoService;
    use App\Services\CountryCitiesService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\Rule;
    use Illuminate\Validation\ValidationException;

    class CityController extends Controller
    {
        protected MeteoService $meteoService;
        protected CountryCitiesService $countryCitiesService;

        public function __construct(MeteoService $meteoService, CountryCitiesService $countryCitiesService)
        {
            $this->meteoService = $meteoService;
            $this->countryCitiesService = $countryCitiesService;
        }

        /**
         * Display a list of all cities.
         */
        public function index()
        {
            $cities = City::orderBy('name')->paginate(15);
            return view('cities.index', compact('cities'));
        }

        /**
         * Show the form for creating a new city.
         */
        public function create()
        {
            // Retrieve dynamic list of countries from the CountryCitiesService.
            $countries = $this->countryCitiesService->getCountries();
            return view('cities.create', compact('countries'));
        }

        /**
         * Store a newly created city in the database.
         */
        public function store(Request $request)
        {

            try {
                $validated = $request->validate([
                    'name'    => 'required|max:255|unique:cities,name',
                    'country' => ['required', 'size:2'],
                ]);
            } catch (ValidationException $e) {
                // Optionally log or modify error messages
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }

            // Use the MeteoService to get coordinates for the city.
            $coords = $this->meteoService->getCoordinates($validated['name'], $validated['country']);
            if (!$coords) {
                return redirect()->back()
                    ->withErrors(['name' => 'Could not retrieve coordinates for the selected city and country.'])
                    ->withInput();
            }

            $validated['latitude']  = $coords['latitude'];
            $validated['longitude'] = $coords['longitude'];

            City::create($validated);

            return redirect()->route('cities.index')
                ->with('success', 'City created successfully.');
        }






        /**
         * Delete a city from the database.
         */
        public function destroy(City $city)
        {
            $city->delete();

            return redirect()->route('cities.index')
                ->with('success', 'City deleted successfully.');
        }
    }
