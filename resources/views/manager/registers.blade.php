@extends('layouts.app')

@section('content')
    <registers-component :role="'manager'" :registers="{{$students}}"></registers-component>
@endsection