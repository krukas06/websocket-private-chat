@extends('layouts.app')

@section('content')
    <div class="container">
        <private-chat :room="{{$room}}"></private-chat>
    </div>
@endsection
