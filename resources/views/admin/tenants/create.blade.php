@extends('layouts.admin')

@section('title','Add Tenant')

@section('page-title','Add Tenant')


@section('content')


<div class="bg-white rounded-xl shadow p-6">


<form method="POST"
action="{{ route('admin.tenants.store') }}">

@csrf


<div class="space-y-4">


<div>

<label class="block mb-1">
Name
</label>

<input name="name"
class="w-full border rounded-lg p-2"
required>

</div>

<div>

<label class="block mb-1">
Phone
</label>

<input name="phone"
class="w-full border rounded-lg p-2"
required>

</div>



<div>

<label class="block mb-1">
Email
</label>

<input name="email"
class="w-full border rounded-lg p-2">

</div>



<div>

<label class="block mb-1">
Property
</label>


<select name="property_id"
class="w-full border rounded-lg p-2">


@foreach($properties as $property)

<option value="{{ $property->id }}">

{{ $property->name }}

</option>

@endforeach


</select>


</div>



<div>

<label class="block mb-1">
Move In Date
</label>


<input type="date"
name="move_in_date"
class="w-full border rounded-lg p-2"
required>


</div>



<button
class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

Save Tenant

</button>


</div>


</form>


</div>


@endsection