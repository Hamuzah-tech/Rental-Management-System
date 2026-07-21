<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenants Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0F172A;
        }
        .header h1 {
            font-size: 22px;
            margin: 0;
            color: #0F172A;
        }
        .header p {
            color: #6B7280;
            margin: 5px 0 0;
            font-size: 11px;
        }
        .filters {
            background: #F8FAFC;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
            border: 1px solid #E5E7EB;
        }
        .filters strong {
            color: #111827;
        }
        .badge {
            background: #E5E7EB;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #F8FAFC;
            color: #111827;
            font-weight: 600;
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #E5E7EB;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 8px 12px;
            border: 1px solid #E5E7EB;
            color: #374151;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #FAFBFC;
        }
        .footer {
            text-align: center;
            color: #6B7280;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            background-color: #F3F4F6;
            color: #374151;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tenant List</h1>
        <p>Alendi: For Landlords. For Tenants.</p>
        <p>Generated on {{ $generatedAt->format('d M Y, H:i') }}</p>
        @if(isset($landlord))
            <p>Landlord: {{ $landlord->name }}</p>
        @endif
        @if(isset($property))
            <p>Property: {{ $property->name }}</p>
        @endif
    </div>

    @if(isset($month) || (isset($paymentStatus) && $paymentStatus != 'all'))
        <div class="filters">
            <strong>Filters Applied:</strong>
            @if(isset($month) && $month)
                <span class="badge">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span>
            @endif
            @if(isset($paymentStatus) && $paymentStatus != 'all')
                <span class="badge">{{ ucfirst($paymentStatus) }}</span>
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tenant Code</th>
                <th>Tenant Name</th>
                <th>Phone</th>
                <th>Property</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $index => $tenant)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tenant->tenant_code }}</td>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->phone }}</td>
                    <td>{{ $tenant->property->name ?? $property->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6B7280; padding: 20px;">
                        No tenants found matching the filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Tenants: {{ $tenants->count() }}
    </div>
</body>
</html>