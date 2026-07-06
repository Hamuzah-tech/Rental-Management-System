@extends('layouts.admin')

@section('title','Dashboard')

@section('page-title','Dashboard')

@section('content')

<div class="card">

    <h2>Welcome {{ auth()->user()->name }}</h2>

    <br>

    <p>
        Welcome to the Rental Management System.
    </p>

</div>

@endsection