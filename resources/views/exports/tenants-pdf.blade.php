<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tenants Report</title>

    <style>
        @page{
            margin:25px;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:11px;
            color:#333;
        }

        h2{
            text-align:center;
            margin:0;
            color:#0d47a1;
        }

        .subtitle{
            text-align:center;
            margin:6px 0 20px;
            color:#666;
            font-size:11px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin:auto;
        }

        th{
            background:#0d47a1;
            color:white;
            border:1px solid #444;
            padding:9px;
            text-align:center;
            font-size:11px;
        }

        td{
            border:1px solid #bbb;
            padding:8px;
            text-align:center;
            vertical-align:middle;
        }

        tbody tr:nth-child(even){
            background:#f7f9fc;
        }

        .paid{
            color:#0a8f08;
            font-weight:bold;
        }

        .partial{
            color:#d48806;
            font-weight:bold;
        }

        .unpaid{
            color:#d32f2f;
            font-weight:bold;
        }

        .footer{
            margin-top:20px;
            text-align:right;
            font-size:10px;
            color:#777;
        }
    </style>
</head>
<body>

<h2>Tenant Report</h2>

<div class="subtitle">
    Rental Management System<br>
    Generated on {{ now()->format('d M Y, H:i') }}
</div>

<table>

    <thead>
        <tr>
            <th>#</th>
            <th>Tenant Code</th>
            <th>Tenant Name</th>
            <th>Phone</th>
            <th>Property</th>
            <th>Monthly Rent (MWK)</th>
            <th>Total Paid</th>
            <th>Balance</th>
            <th>Payment Status</th>
            <th>Tenant Status</th>
        </tr>
    </thead>

    <tbody>

    @forelse($tenants as $tenant)

        @php
            // Adjust these relationships/fields to match your models
            $paid = $tenant->payments->sum('amount') ?? 0;
            $rent = $tenant->monthly_rent ?? 0;
            $balance = max($rent - $paid, 0);

            if($paid >= $rent && $rent > 0){
                $paymentStatus = 'Paid';
                $class = 'paid';
            }elseif($paid > 0){
                $paymentStatus = 'Partial';
                $class = 'partial';
            }else{
                $paymentStatus = 'Unpaid';
                $class = 'unpaid';
            }
        @endphp

        <tr>

            <td>{{ $loop->iteration }}</td>

            <td>{{ $tenant->tenant_code }}</td>

            <td>{{ $tenant->name }}</td>

            <td>{{ $tenant->phone }}</td>

            <td>{{ $tenant->property->name ?? '-' }}</td>

            <td>{{ number_format($rent) }}</td>

            <td>{{ number_format($paid) }}</td>

            <td>{{ number_format($balance) }}</td>

            <td class="{{ $class }}">
                {{ $paymentStatus }}
            </td>

            <td>
                {{ ucfirst($tenant->status) }}
            </td>

        </tr>

    @empty

        <tr>
            <td colspan="10">
                No tenant records found.
            </td>
        </tr>

    @endforelse

    </tbody>

</table>

<div class="footer">
    Total Tenants: <strong>{{ $tenants->count() }}</strong>
</div>

</body>
</html>