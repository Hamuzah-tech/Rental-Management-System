@extends('layouts.admin')

@section('title','Properties')

@section('page-title','Properties')


@section('content')


<div class="space-y-6">


<div class="bg-white rounded-xl shadow p-6 flex justify-between items-center">


<div>

<h2 class="text-2xl font-bold">
Properties
</h2>

<p class="text-gray-500">
Manage all properties.
</p>

</div>


<a href="{{ route('admin.properties.create') }}"
class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

+ Add Property

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
Property
</th>

<th class="px-6 py-4 text-left">
Owner
</th>


<th class="px-6 py-4 text-left">
Tenants
</th>


<th class="px-6 py-4 text-left">
Status
</th>


<th class="px-6 py-4">
Actions
</th>

</tr>

</thead>


<tbody>


@forelse($properties as $property)


<tr class="border-t">


<td class="px-6 py-4">

{{ $property->name }}

</td>


<td class="px-6 py-4">

{{ $property->landlord->name }}

</td>


<td class="px-6 py-4">

{{ $property->tenants_count ?? 0 }}

</td>


<td class="px-6 py-4">


@if($property->status)

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
Active
</span>

@else

<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
Inactive
</span>

@endif


</td>


<td class="px-6 py-4 space-x-2">


<a href="{{ route('admin.properties.show',$property) }}"
class="text-blue-600">

View

</a>


<a href="{{ route('admin.properties.edit',$property) }}"
class="text-indigo-600">

Edit

</a>



<form method="POST"
action="{{ route('admin.properties.destroy',$property) }}"
class="inline">

@csrf
@method('DELETE')

<button
onclick="return confirm('Delete property?')"
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

No properties found.

</td>

</tr>


@endforelse


</tbody>


</table>


</div>



{{ $properties->links() }}


</div>


@endsection