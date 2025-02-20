@extends('layouts.app')
@section('title', 'Temperature Records')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h1>Temperature Records</h1>
        <a href="{{ route('temperatures.create') }}" class="btn btn-primary">Add Temperature</a>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>City</th>
            <th>Temperature</th>
            <th>Unit</th>
            <th>Recorded At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($temperatures as $temperature)
            <tr>
                <td>{{ $temperature->id }}</td>
                <td>{{ $temperature->city->name }}</td>
                <td>{{ $temperature->value }}</td>
                <td>{{ $temperature->unit }}</td>
                <td>{{ $temperature->recorded_at }}</td>
                <td>
                    <a href="{{ route('temperatures.edit', $temperature) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('temperatures.destroy', $temperature) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div>
        {{ $temperatures->links() }}
    </div>
@endsection
