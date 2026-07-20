<!-- resources/views/landlord/properties/trashed.blade.php -->
@extends('layouts.landlord')

@section('title', 'Archived Properties')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Archived Properties</h1>
            <p class="text-slate-500 text-sm">Properties moved to archive can be restored</p>
        </div>
        <a href="{{ route('landlord.properties.index') }}" 
           class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-xl text-sm transition">
            ← Back to Properties
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @if($properties->count() > 0)
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Property Name</th>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Address</th>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Deleted On</th>
                        <th class="text-center px-6 py-3 text-sm font-medium text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($properties as $property)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-800">{{ $property->name }}</div>
                                <div class="text-xs text-slate-500">#{{ $property->id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $property->address ?? 'No address' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $property->deleted_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('landlord.properties.restore', $property->id) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $properties->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <p class="text-slate-500">No archived properties found.</p>
                <p class="text-slate-400 text-sm">Deleted properties will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection