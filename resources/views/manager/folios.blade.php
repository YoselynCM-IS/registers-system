@extends('layouts.app')

@section('content')
    <folios-component :registers="{{$folios}}" :role="'manager'">
    </folios-component>
@endsection