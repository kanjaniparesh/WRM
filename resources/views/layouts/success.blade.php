@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ $url }}/home">Back</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div style="display: flex;justify-content: center;height:500px">
                <img src="{{ asset('img/success.gif') }}">
            </div>
            <div style="display: flex;justify-content: center;">
                <div>Please click on below link or copy the link</div>
            </div>
            <div style="display: flex;justify-content: center;">
                <a href="{{ $url }}/s/{{ $data->code }}" target="_blank">{{ $url }}/s/{{ $data->code }}</a>
            </div>
            <div style="display: flex;justify-content: center;">
                <div>This link is only available for 7 days from created date.</div>
            </div>
        </div>
    </div>
</div>
@endsection