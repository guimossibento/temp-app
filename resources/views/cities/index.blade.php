@extends('layouts.app')
@section('title', 'Cities')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h1>Cities</h1>
        <a href="{{ route('cities.create') }}" class="btn btn-primary">Add City</a>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Country Code</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($cities as $city)
            <tr>
                <td>{{ $city->id }}</td>
                <td>{{ $city->name }}</td>
                <td>{{ $city->country }}</td>
                <td>
                    <form action="{{ route('cities.destroy', $city) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $cities->links() }}
@endsection
