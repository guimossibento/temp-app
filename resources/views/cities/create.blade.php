@extends('layouts.app')
@section('title', 'Add City')
@section('content')
    <div class="container">
        <h1>Add City</h1>
        <form action="{{ route('cities.store') }}" method="POST">
            @csrf
            <!-- Country Select -->
            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <select id="country" name="country" class="form-select" required>
                    <option value="">-- Select a Country --</option>
                </select>
                @error('country')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- City Select -->
            <div class="mb-3">
                <label for="name" class="form-label">City</label>
                <select id="name" name="name" class="form-select" required>
                    <option value="">-- Select a City --</option>
                    <!-- Options loaded dynamically -->
                </select>
                @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

@section('scripts')
    <!-- Include jQuery and Select2 from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize the country select with AJAX and client-side filtering.
            $('#country').select2({
                placeholder: "Select a country",
                allowClear: true,
                ajax: {
                    url: '/api/countries',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data, params) {
                        params.term = params.term || "";
                        var results = [];
                        // data.data is expected as an array of objects: { code, name }
                        $.each(data.data, function(index, country) {
                            if (params.term === "" || country.name.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                results.push({
                                    id: country.code, // ISO2 code will be saved
                                    text: country.name + " (" + country.code + ")",
                                    value: country.code
                                });
                            }
                        });
                        return { results: results };
                    },
                    cache: true
                }
            });

            // Initialize the city select with AJAX; its URL depends on the selected country's ISO2 code.
            $('#name').select2({
                placeholder: "Select a city",
                allowClear: true,
                ajax: {
                    url: function() {
                        var countryCode = $('#country').val();
                        return '/api/countries/' + encodeURIComponent(countryCode) + '/cities';
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data, params) {
                        params.term = params.term || "";
                        var results = [];
                        if (data.data) {
                            $.each(data.data, function(index, city) {
                                // Filter cities on client side based on the search term.
                                if (params.term === "" || city.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                    results.push({ id: city, text: city });
                                }
                            });
                        }
                        return { results: results };
                    },
                    cache: true
                }
            });

            // When a country is selected, clear the city select.
            $('#country').on('change', function() {
                $('#name').empty().append('<option value="">-- Select a City --</option>').trigger('change');
            });
        });
    </script>
@endsection
