@extends('layouts.admin')

@section('title','Tenant Details')

@section('page-title','Tenant Details')


@section('content')


<div class="space-y-6">


<div class="bg-white rounded-xl shadow p-6">


<div class="flex justify-between items-center mb-6">


<div>

<h2 class="text-2xl font-bold">
{{ $tenant->name }}
</h2>

<p class="text-gray-500">
Tenant Information
</p>

</div>


<a href="{{ route('admin.tenants.index') }}"
class="bg-gray-600 text-white px-5 py-2 rounded-lg">

Back

</a>


</div>




<div class="grid grid-cols-1 md:grid-cols-2 gap-6">


<div>

<p class="text-gray-500">
Name
</p>

<p class="font-semibold">
{{ $tenant->name }}
</p>

</div>



<div>

<p class="text-gray-500">
Phone
</p>

<p class="font-semibold">
{{ $tenant->phone }}
</p>

</div>



<div>

<p class="text-gray-500">
Email
</p>

<p class="font-semibold">
{{ $tenant->email ?? 'N/A' }}
</p>

</div>



<div>

<p class="text-gray-500">
Move In Date
</p>

<p class="font-semibold">
{{ $tenant->move_in_date }}
</p>

</div>



<div>

<p class="text-gray-500">
Property
</p>

<p class="font-semibold">

{{ $tenant->property->name ?? 'N/A' }}

</p>

</div>



<div>

<p class="text-gray-500">
Address
</p>

<p class="font-semibold">

{{ $tenant->property->address ?? 'N/A' }}

</p>

</div>



</div>


</div>




<div class="bg-white rounded-xl shadow p-6">


<h3 class="text-xl font-bold mb-4">
Actions
</h3>


<a href="{{ route('admin.tenants.edit',$tenant) }}"
class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

Edit Tenant

</a>



<form method="POST"
action="{{ route('admin.tenants.destroy',$tenant) }}"
class="inline">

@csrf
@method('DELETE')


<button
onclick="return confirm('Delete tenant?')"
class="bg-red-600 text-white px-5 py-2 rounded-lg ml-2">

Delete Tenant

</button>


</form>


</div>



</div>


@endsection