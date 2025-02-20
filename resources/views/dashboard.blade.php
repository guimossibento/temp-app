@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>
        <div class="row">
            @foreach($cities as $city)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            {{ $city->name }} ({{$city->country}})
                        </div>
                        <div class="card-body">
                            <!-- Each canvas has a data attribute for the city id -->
                            <canvas id="chart-city-{{ $city->id }}" data-city-id="{{ $city->id }}" height="300"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection


@section('scripts')
    <!-- Include jQuery and Chart.js from Vite bundle (if not loaded globally elsewhere) -->
    @vite(['resources/js/dashboard.js'])
@endsection
