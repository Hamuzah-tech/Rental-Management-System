@extends('layouts.admin')

@section('title','Property Details')

@section('page-title','Property Details')


@section('content')


<div class="space-y-6">



<div class="bg-white rounded-xl shadow p-6">


<div class="flex justify-between">


<div>

<h2 class="text-3xl font-bold">

{{ $property->name }}

</h2>


<p class="text-gray-500">

{{ $property->address }}

</p>


</div>


@if($property->status)

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full h-fit">

Active

</span>

@endif


</div>


<hr class="my-6">


<div class="grid md:grid-cols-2 gap-6">


<div>

<h3 class="font-bold">
Owner
</h3>

<p>
{{ $property->landlord->name }}
</p>


</div>



<div>

<h3 class="font-bold">
Total Tenants
</h3>

<p>

{{ $property->tenants->count() }}

</p>


</div>


</div>



</div>




<div class="bg-white rounded-xl shadow">


<div class="px-6 py-4 border-b">

<h3 class="text-xl font-bold">
Tenants
</h3>

</div>



<table class="w-full">


<thead class="bg-gray-100">

<tr>

<th class="px-6 py-3 text-left">
Name
</th>

<th class="px-6 py-3 text-left">
Phone
</th>

<th class="px-6 py-3 text-left">
Status
</th>

</tr>

</thead>


<tbody>


@forelse($property->tenants as $tenant)


<tr class="border-t">


<td class="px-6 py-3">
{{ $tenant->name }}
</td>


<td class="px-6 py-3">
{{ $tenant->phone }}
</td>


<td class="px-6 py-3">

{{ $tenant->status }}

</td>


</tr>


@empty


<tr>

<td colspan="3"
class="text-center py-6 text-gray-500">

No tenants yet.

</td>

</tr>


@endforelse


</tbody>


</table>


</div>



</div>


@endsection