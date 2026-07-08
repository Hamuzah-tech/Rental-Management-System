@extends('layouts.landlord')

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
Manage your tenants.
</p>

</div>



<a href="{{ route('landlord.tenants.create') }}"
class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

+ Add Tenant

</a>


</div>





@if(session('success'))

<div class="bg-green-100 text-green-700 p-4 rounded-lg">

{{ session('success') }}

</div>

@endif





<div class="bg-white rounded-xl shadow overflow-hidden">


<table class="w-full">


<thead class="bg-gray-100">


<tr>


<th class="px-6 py-4 text-left">
Tenant Code
</th>


<th class="px-6 py-4 text-left">
Name
</th>


<th class="px-6 py-4 text-left">
Phone
</th>


<th class="px-6 py-4 text-left">
Property
</th>


<th class="px-6 py-4 text-left">
Rent
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


@forelse($tenants as $tenant)


<tr class="border-t">


<td class="px-6 py-4 font-bold text-indigo-600">

{{ $tenant->tenant_code }}

</td>



<td class="px-6 py-4">

{{ $tenant->name }}

</td>



<td class="px-6 py-4">

{{ $tenant->phone }}

</td>



<td class="px-6 py-4">

{{ $tenant->property->name }}

</td>



<td class="px-6 py-4">

{{ number_format($tenant->monthly_rent) }}

</td>




<td class="px-6 py-4">


@if($tenant->status == 'Active')

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">

Active

</span>

@else

<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full">

Moved Out

</span>

@endif


</td>





<td class="px-6 py-4 space-x-2">



<a href="{{ route('landlord.tenants.edit',$tenant) }}"
class="text-indigo-600">

Edit

</a>





@if($tenant->status == 'Active')


<form method="POST"
action="{{ route('landlord.tenants.moveout',$tenant) }}"
class="inline">

@csrf
@method('PATCH')


<button class="text-orange-600">

Move Out

</button>


</form>



@else


<form method="POST"
action="{{ route('landlord.tenants.reactivate',$tenant) }}"
class="inline">

@csrf
@method('PATCH')


<button class="text-green-600">

Reactivate

</button>


</form>


@endif






<form method="POST"
action="{{ route('landlord.tenants.destroy',$tenant) }}"
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

<td colspan="7"
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