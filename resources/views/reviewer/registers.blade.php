@extends('layouts.app')

@section('content')
    <registers-component :role="'reviewer'" :registers="{{$students}}"></registers-component>
@endsection