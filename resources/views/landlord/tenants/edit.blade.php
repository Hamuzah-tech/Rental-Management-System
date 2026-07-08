@extends('layouts.landlord')

@section('title','Edit Tenant')

@section('page-title','Edit Tenant')


@section('content')


<div class="bg-white rounded-xl shadow p-6">


<form method="POST"
action="{{ route('landlord.tenants.update',$tenant) }}">


@csrf

@method('PUT')



<div class="space-y-5">



<div>

<label>
Full Name
</label>


<input
name="name"
value="{{ $tenant->name }}"
class="w-full border rounded-lg p-3"
required>

</div>




<div>

<label>
Phone
</label>


<input
name="phone"
value="{{ $tenant->phone }}"
class="w-full border rounded-lg p-3"
required>


</div>




<div>

<label>
Email
</label>


<input
name="email"
value="{{ $tenant->email }}"
class="w-full border rounded-lg p-3">


</div>





<div>

<label>
Property
</label>


<select
name="property_id"
class="w-full border rounded-lg p-3">


@foreach($properties as $property)

<option
value="{{ $property->id }}"
@if($tenant->property_id == $property->id)
selected
@endif
>

{{ $property->name }}

</option>


@endforeach


</select>


</div>






<div>

<label>
Monthly Rent
</label>


<input
type="number"
name="monthly_rent"
value="{{ $tenant->monthly_rent }}"
class="w-full border rounded-lg p-3"
required>


</div>






<button
class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

Update Tenant

</button>




</div>


</form>


</div>


@endsection