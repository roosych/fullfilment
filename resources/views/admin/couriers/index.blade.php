@extends('layouts.dashboard')

@section('title', 'Кабинет')

@section('content')
    couriers index

    <pre>{{ print_r($couriers->toArray(), true) }}</pre>

@endsection
