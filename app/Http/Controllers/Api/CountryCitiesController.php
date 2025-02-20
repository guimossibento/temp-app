<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Services\CountryCitiesService;
    use Illuminate\Http\JsonResponse;

    class CountryCitiesController extends Controller
    {
        protected CountryCitiesService $countryCitiesService;

        public function __construct(CountryCitiesService $countryCitiesService)
        {
            $this->countryCitiesService = $countryCitiesService;
        }

        /**
         * Return a list of countries.
         *
         * @return JsonResponse
         */
        public function countries()
        {
            // This method returns an associative array where both keys and values are country names.
            $countries = $this->countryCitiesService->getCountries();
            return response()->json(['data' => $countries]);
        }

        /**
         * Return a list of cities for the given country.
         *
         * @param string $country The full country name.
         * @return JsonResponse
         */
        public function cities($country)
        {
            $cities = $this->countryCitiesService->getCities($country);
            return response()->json(['data' => $cities]);
        }
    }
