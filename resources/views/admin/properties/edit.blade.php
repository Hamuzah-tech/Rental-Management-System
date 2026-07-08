@extends('layouts.admin')

@section('title','Edit Property')

@section('page-title','Edit Property')


@section('content')

<div class="max-w-4xl mx-auto">


<div class="bg-white rounded-xl shadow">


<div class="px-6 py-4 border-b">

<h2 class="text-2xl font-bold">
Edit Property
</h2>

</div>



<form method="POST"
action="{{ route('admin.properties.update',$property) }}">

@csrf
@method('PUT')


<div class="p-6 space-y-6">


<div>

<label class="block font-medium mb-2">
Property Name
</label>


<input
type="text"
name="name"
value="{{ old('name',$property->name) }}"
class="w-full rounded-lg border-gray-300">


</div>



<div>

<label class="block font-medium mb-2">
Owner
</label>


<select
name="landlord_id"
class="w-full rounded-lg border-gray-300">


@foreach($landlords as $landlord)

<option
value="{{ $landlord->id }}"
@if($property->landlord_id==$landlord->id)
selected
@endif
>

{{ $landlord->name }}

</option>

@endforeach


</select>


</div>



<div>

<label class="block font-medium mb-2">
Address
</label>


<input
type="text"
name="address"
value="{{ old('address',$property->address) }}"
class="w-full rounded-lg border-gray-300">


</div>



<div>

<label class="block font-medium mb-2">
Description
</label>


<textarea
name="description"
rows="4"
class="w-full rounded-lg border-gray-300">{{ old('description',$property->description) }}</textarea>


</div>


</div>




<div class="border-t px-6 py-4 flex justify-end">


<button
class="bg-indigo-600 text-white px-6 py-2 rounded-lg">

Update Property

</button>


</div>



</form>


</div>


</div>


@endsection