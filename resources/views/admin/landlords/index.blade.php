@extends('layouts.admin')

@section('title','Landlords')

@section('page-title','Landlords')

@section('content')

<div class="card">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">

        <h2>Landlords</h2>

        <a href="{{ route('admin.landlords.create') }}">
            + Create Landlord
        </a>

    </div>

    <table width="100%" border="1" cellpadding="10">

        <tr>

            <th>Name</th>

            <th>Username</th>

            <th>Phone</th>

            <th>Status</th>

        </tr>

        @forelse($landlords as $landlord)

            <tr>

                <td>{{ $landlord->name }}</td>

                <td>{{ $landlord->username }}</td>

                <td>{{ $landlord->phone }}</td>

                <td>{{ $landlord->status ? 'Active' : 'Suspended' }}</td>

            </tr>

        @empty

            <tr>

                <td colspan="4">

                    No landlords found.

                </td>

            </tr>

        @endforelse

    </table>

</div>

@endsection