@extends('layouts.admin')

@section('title','Edit Tenant')

@section('page-title','Edit Tenant')


@section('content')


<div class="bg-white rounded-xl shadow p-6">


<form method="POST"
action="{{ route('admin.tenants.update',$tenant) }}">

@csrf
@method('PUT')


<div class="space-y-4">


<input name="name"
value="{{ $tenant->name }}"
class="w-full border rounded-lg p-2">


<input name="phone"
value="{{ $tenant->phone }}"
class="w-full border rounded-lg p-2">


<input name="email"
value="{{ $tenant->email }}"
class="w-full border rounded-lg p-2">


<select name="property_id"
class="w-full border rounded-lg p-2">


@foreach($properties as $property)

<option value="{{ $property->id }}"
@if($tenant->property_id == $property->id)
selected
@endif>

{{ $property->name }}

</option>

@endforeach


</select>



<input type="date"
name="move_in_date"
value="{{ $tenant->move_in_date }}"
class="w-full border rounded-lg p-2">



<button
class="bg-green-600 text-white px-5 py-2 rounded-lg">

Update Tenant

</button>


</div>


</form>


</div>


@endsection