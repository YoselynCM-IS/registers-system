@extends('layouts.app')

@section('content')
    <registers-component :role="'administrator'" :registers="{{$students}}"></registers-component>
@endsection