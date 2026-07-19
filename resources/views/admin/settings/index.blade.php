@extends('layouts.admin')

@section('title', 'Settings')

@section('page-title', 'Settings')

@section('content')

<div class="bg-white rounded-xl shadow p-6">

    <h2 class="text-2xl font-bold mb-2">
        Database Backup
    </h2>

    <p class="text-gray-500 mb-6">
        Create and download full database backups.
    </p>

    <form action="{{ route('admin.settings.backup') }}" method="POST">
        @csrf

        <button
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

            Create Backup

        </button>

    </form>

</div>

@endsection