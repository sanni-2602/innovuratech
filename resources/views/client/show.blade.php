@extends('default')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                Name: {{ $client->name }}<br>
                Mobile Number: {{ $client->mobile_number }}<br>
                Email: {{ $client->email }}<br>
                Gender: {{ $client->gender }}<br>
                State: {{ $client->state }}<br>
                City: {{ $client->city }}<br>
                Address: {{ $client->address }}
            </div>
        </div>
    </div>
@endsection
