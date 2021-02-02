@extends('layouts.app')

@section('content')
    <pre-register-component :registers="{{$schools}}"></pre-register-component>
    <!-- <register-component :registers="{{$schools}}"></register-component> -->
@endsection
