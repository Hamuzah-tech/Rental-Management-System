@extends('layouts.landlord')

@section('title', 'Archived Tenants')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="page-header mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Archived Tenants</h1>
            <p class="text-slate-500 text-sm">Tenants moved to archive can be restored</p>
        </div>
        <a href="{{ route('landlord.tenants.index') }}"
           class="bg-[#ca0251] hover:bg-[#a80244] text-white px-4 py-2 rounded-xl text-sm transition w-full sm:w-auto text-center">
            ← Back to Tenants
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @if($tenants->count() > 0)
            <div class="table-scroll">
            <table class="w-full min-w-[720px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Tenant</th>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Property</th>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Contact</th>
                        <th class="text-left px-6 py-3 text-sm font-medium text-slate-600">Deleted On</th>
                        <th class="text-center px-6 py-3 text-sm font-medium text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($tenants as $tenant)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-800">{{ e($tenant->name) }}</div>
                                <div class="text-xs text-slate-500">Code: {{ e($tenant->tenant_code) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ e($tenant->property->name ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-600">{{ e($tenant->email) }}</div>
                                <div class="text-sm text-slate-500">{{ e($tenant->phone) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $tenant->deleted_at ? $tenant->deleted_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('landlord.tenants.restore', $tenant->public_id) }}" 
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to restore this tenant?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-[#ca0251] hover:text-[#a80244] text-sm font-medium transition">
                                        Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="px-4 sm:px-6 py-4 border-t border-slate-200 overflow-x-auto">
                {{ $tenants->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <p class="text-slate-500">No archived tenants found.</p>
                <p class="text-slate-400 text-sm">Deleted tenants will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection