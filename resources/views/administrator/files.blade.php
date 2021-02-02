@extends('layouts.app')

@section('content')
    <vouchers-component :registers="{{$files}}"></vouchers-component>
@endsection