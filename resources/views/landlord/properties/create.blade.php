@extends('layouts.landlord')

@section('title','Add Property')

@section('page-title','Add Property')


@section('content')


<div class="bg-white rounded-xl shadow p-6">


<form method="POST"
action="{{ route('landlord.properties.store') }}">

@csrf


<div class="space-y-5">


<div>

<label class="block mb-1 font-medium">
Property Name
</label>

<input
type="text"
name="name"
class="w-full border rounded-lg p-3"
placeholder="Example: Sunrise Hostel"
required>

</div>



<div>

<label class="block mb-1 font-medium">
Address (Optional)
</label>

<input
type="text"
name="address"
class="w-full border rounded-lg p-3"
placeholder="Example: Blantyre">

</div>




<div>

<label class="block mb-1 font-medium">
Description (Optional)
</label>

<textarea
name="description"
class="w-full border rounded-lg p-3"
rows="4"
placeholder="Property description"></textarea>

</div>



<button
class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

Save Property

</button>



</div>


</form>


</div>


@endsection