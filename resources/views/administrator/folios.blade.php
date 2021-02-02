@extends('layouts.app')

@section('content')
    <folios-component :registers="{{$folios}}" :role="'administrator'">
    </folios-component>
@endsection