@extends('layouts.app')
@section('title', 'Add Temperature Record')
@section('content')
    <h1>Add Temperature Record</h1>
    <form action="{{ route('temperatures.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="city_id" class="form-label">City</label>
            <select name="city_id" id="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                <option value="">Select City</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>
            @error('city_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="value" class="form-label">Temperature</label>
            <input type="number" step="0.01" name="value" id="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}" required>
            @error('value')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <select name="unit" id="unit" class="form-select @error('unit') is-invalid @enderror" required>
                <option value="C" {{ old('unit') == 'C' ? 'selected' : '' }}>Celsius</option>
                <option value="F" {{ old('unit') == 'F' ? 'selected' : '' }}>Fahrenheit</option>
            </select>
            @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="recorded_at" class="form-label">Recorded At</label>
            <input type="datetime-local" name="recorded_at" id="recorded_at" class="form-control @error('recorded_at') is-invalid @enderror" value="{{ old('recorded_at') }}" required>
            @error('recorded_at')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
