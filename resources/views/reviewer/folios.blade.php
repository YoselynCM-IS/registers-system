@extends('layouts.app')

@section('content')
    <folios-component :registers="{{$folios}}" :role="'reviewer'">
    </folios-component>
@endsection