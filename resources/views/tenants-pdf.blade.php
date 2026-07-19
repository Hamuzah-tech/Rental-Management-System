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
    </style>
</head>
<body>
    <div class="header">
        <h1>Tenants List</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tenant Code</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Property</th>
                <th>Monthly Rent (MK)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $index => $tenant)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tenant->tenant_code }}</td>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->phone }}</td>
                    <td>{{ $tenant->property->name ?? 'N/A' }}</td>
                    <td>{{ number_format($tenant->monthly_rent, 2) }}</td>
                    <td>
                        <span class="status-badge">{{ $tenant->status }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8;">
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