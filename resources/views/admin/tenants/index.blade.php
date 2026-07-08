@extends('layouts.admin')

@section('title','Tenants')

@section('page-title','Tenants')


@section('content')

<div class="space-y-6">


<div class="bg-white rounded-xl shadow p-6 flex justify-between items-center">


<div>

<h2 class="text-2xl font-bold">
Tenants
</h2>

<p class="text-gray-500">
Manage all tenants.
</p>

</div>


<a href="{{ route('admin.tenants.create') }}"
class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

+ Add Tenant

</a>


</div>



@if(session('success'))

<div class="bg-green-100 text-green-800 px-5 py-3 rounded-lg">

{{ session('success') }}

</div>

@endif




<div class="bg-white rounded-xl shadow overflow-hidden">


<table class="w-full">


<thead class="bg-gray-100">

<tr>

<th class="px-6 py-4 text-left">
Tenant
</th>


<th class="px-6 py-4 text-left">
Phone
</th>


<th class="px-6 py-4 text-left">
Property
</th>


<th class="px-6 py-4 text-left">
Move In
</th>


<th class="px-6 py-4">
Actions
</th>

</tr>

</thead>



<tbody>


@forelse($tenants as $tenant)


<tr class="border-t">


<td class="px-6 py-4">

{{ $tenant->name }}

</td>



<td class="px-6 py-4">

{{ $tenant->phone }}

</td>



<td class="px-6 py-4">

{{ $tenant->property->name ?? 'N/A' }}

</td>



<td class="px-6 py-4">

{{ $tenant->move_in_date }}

</td>



<td class="px-6 py-4 space-x-2">


<a href="{{ route('admin.tenants.edit',$tenant) }}"
class="text-indigo-600">

Edit

</a>



<form method="POST"
action="{{ route('admin.tenants.destroy',$tenant) }}"
class="inline">

@csrf
@method('DELETE')

<button
onclick="return confirm('Delete tenant?')"
class="text-red-600">

Delete

</button>

</form>


</td>


</tr>


@empty


<tr>

<td colspan="5"
class="text-center py-6 text-gray-500">

No tenants found.

</td>

</tr>


@endforelse


</tbody>


</table>


</div>



{{ $tenants->links() }}


</div>


@endsection