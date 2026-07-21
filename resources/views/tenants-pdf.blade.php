<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenants List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #1e293b;
        }
        .header p {
            color: #64748b;
            margin: 5px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            background-color: #f1f5f9;
            color: #475569;
            font-size: 11px;
        }
        .filters {
            background: #f8fafc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
            border: 1px solid #e2e8f0;
        }
        .filters strong {
            color: #1e293b;
        }
        .badge {
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>@if(isset($property)) {{ $property->name }} - @endif Tenants List</h1>
        <p>Generated on {{ $generatedAt->format('F d, Y H:i') }}</p>
        @if(isset($landlord))
            <p>Landlord: {{ $landlord->name }}</p>
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
                    <td colspan="5" style="text-align: center; color: #94a3b8;">
                        No tenants found
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