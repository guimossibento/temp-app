<?php

    namespace App\Http\Controllers;

    use App\Models\City;

    class DashboardController extends Controller
    {
        public function index()
        {
            // Retrieve all cities with their temperature records sorted by recorded time and has at least one temperature
            $cities = City::with([
                'temperatures' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                }
            ])
            ->whereHas('temperatures')
            ->get();

            return view('dashboard', compact('cities'));
        }
    }
