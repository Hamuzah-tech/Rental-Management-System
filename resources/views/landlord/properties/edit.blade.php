@extends('layouts.landlord')

@section('title','Edit Property')

@section('page-title','Edit Property')


@section('content')


<div class="bg-white rounded-xl shadow p-6">


<form method="POST"
action="{{ route('landlord.properties.update',$property) }}">

@csrf

@method('PUT')


<div class="space-y-5">


<div>

<label class="block mb-1 font-medium">
Property Name
</label>


<input
name="name"
value="{{ $property->name }}"
class="w-full border rounded-lg p-3"
required>


</div>




<div>

<label class="block mb-1 font-medium">
Address
</label>


<input
name="address"
value="{{ $property->address }}"
class="w-full border rounded-lg p-3">


</div>




<div>

<label class="block mb-1 font-medium">
Description
</label>


<textarea
name="description"
class="w-full border rounded-lg p-3"
rows="4">{{ $property->description }}</textarea>


</div>



<button
class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

Update Property

</button>



</div>


</form>


</div>


@endsection