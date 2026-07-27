<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenant Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1a202c;
            padding: 20px;
        }

        /* Report Header */
        .report-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2d3748;
        }

        .report-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a202c;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .report-subtitle {
            font-size: 14px;
            color: #4a5568;
            font-weight: 500;
        }

        .report-meta {
            font-size: 10px;
            color: #718096;
            margin-top: 8px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .report-meta span {
            display: inline-block;
        }

        /* Filter Info */
        .filter-info {
            background: #f7fafc;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9px;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }

        .filter-info strong {
            color: #2d3748;
        }

        /* Table Styles */
        .table-container {
            width: 100%;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        thead {
            background-color: #2d3748;
        }

        thead th {
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #2d3748;
        }

        tbody td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        /* Zebra striping */
        tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }

        tbody tr:hover {
            background-color: #edf2f7;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-active {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .badge-inactive {
            background-color: #fed7d7;
            color: #742a2a;
        }

        .badge-pending {
            background-color: #fefcbf;
            color: #744210;
        }

        .badge-paid {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .badge-overdue {
            background-color: #fed7d7;
            color: #742a2a;
        }

        /* Currency formatting */
        .currency {
            font-weight: 500;
        }

        /* Footer */
        .footer-note {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #718096;
            text-align: center;
        }

        .last-updated {
            float: right;
            font-size: 8px;
            color: #718096;
        }

        /* Column widths */
        .col-code { width: 8%; }
        .col-name { width: 14%; }
        .col-phone { width: 10%; }
        .col-email { width: 14%; }
        .col-property { width: 12%; }
        .col-landlord { width: 12%; }
        .col-rent { width: 8%; }
        .col-status { width: 8%; }
        .col-movein { width: 8%; }
        .col-tenant-status { width: 6%; }
    </style>
</head>
<body>

    <!-- Report Header -->
    <div class="report-header">
        <div class="report-title">Tenant Report</div>
        <div class="report-subtitle">Rental Management System</div>
        <div class="report-meta">
            <span>📅 Generated: {{ date('F d, Y h:i A') }}</span>
            <span>👥 Total Tenants: {{ $tenants->count() }}</span>
        </div>
    </div>

    <!-- Filter Information -->
    @if($filters['landlord'] || $filters['search'])
    <div class="filter-info">
        <strong>Filtered by:</strong>
        @if($filters['landlord'])
            <span>Landlord: {{ $filters['landlord']->name }}</span>
        @endif
        @if($filters['search'])
            <span>Search: "{{ $filters['search'] }}"</span>
        @endif
    </div>
    @endif

    <!-- Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="col-code">Code</th>
                    <th class="col-name">Tenant Name</th>
                    <th class="col-phone">Phone</th>
                    <th class="col-email">Email</th>
                    <th class="col-property">Property</th>
                    <th class="col-landlord">Landlord</th>
                    <th class="col-rent">Monthly Rent</th>
                    <th class="col-status">Payment Status</th>
                    <th class="col-movein">Move-in Date</th>
                    <th class="col-tenant-status">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td>
                        <strong>{{ $tenant->tenant_code ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->phone }}</td>
                    <td>{{ $tenant->email ?? 'N/A' }}</td>
                    <td>{{ $tenant->property->name ?? 'N/A' }}</td>
                    <td>{{ $tenant->property->landlord->name ?? 'N/A' }}</td>
                    <td class="currency">
                        MK {{ number_format($tenant->monthly_rent ?? 0) }}
                    </td>
                    <td>
                        @php
                            $status = $tenant->payment_status ?? 'Pending';
                            $badgeClass = match($status) {
                                'Paid' => 'badge-paid',
                                'Overdue' => 'badge-overdue',
                                default => 'badge-pending'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                    </td>
                    <td>{{ $tenant->move_in_date ?? 'N/A' }}</td>
                    <td>
                        @php
                            $tenantStatus = $tenant->status ?? 'Active';
                            $statusBadgeClass = $tenantStatus === 'Active' ? 'badge-active' : 'badge-inactive';
                        @endphp
                        <span class="badge {{ $statusBadgeClass }}">{{ $tenantStatus }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 30px; color: #718096;">
                        No tenants found matching the current filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <span>This report is automatically generated by Rental Management System</span>
        <span class="last-updated">Page <span class="page-number"></span> of <span class="page-count"></span></span>
    </div>

    <!-- Page Number Script -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $pdf->getFontMetrics()->getFont("helvetica", "normal");
            $pdf->page_text(750, 570, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        }
    </script>

</body>
</html>