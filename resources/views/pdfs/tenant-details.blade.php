<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenant Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .amount {
            text-align: right;
        }
        .date {
            white-space: nowrap;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
<div class="header">
        <h1>Tenant Details</h1>
        @if($company)
        <div class="company-info">
            <p>{{ $company->name }}</p>
            <p>{{ $company->address }}</p>
            <p>{{ $company->phone_number }}</p>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Tenant Information</div>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $tenant->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $tenant->email }}</td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td>{{ $tenant->phone_number }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="status status-{{ strtolower($tenant->status) }}">
                        {{ $tenant->status }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Tenancy Agreements</div>
        <table>
            <thead>
                <tr>
                    <th>Agreement ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenant->tenancyAgreements as $agreement)
                <tr>
                    <td>{{ $agreement->id }}</td>
                    <td>{{ $agreement->start_date ? date('Y-m-d', strtotime($agreement->start_date)) : '' }}</td>
                    <td>{{ $agreement->end_date ? date('Y-m-d', strtotime($agreement->end_date)) : 'Ongoing' }}</td>
                    <td>
                        @php
                            $agreementStatus = !$agreement->end_date || Carbon\Carbon::parse($agreement->end_date)->isFuture() 
                                ? 'Active' 
                                : 'Inactive';
                        @endphp
                        <span class="status status-{{ strtolower($agreementStatus) }}">
                            {{ $agreementStatus }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No agreements found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Tenancy Bills</div>
        <table>
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Bill Date</th>
                    <th>Name</th>
                    <th class="amount">Total Amount</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td>{{ $bill->id }}</td>
                    <td class="date">{{ $bill->bill_date ? date('Y-m-d', strtotime($bill->bill_date)) : '' }}</td>
                    <td>{{ $bill->name }}</td>
                    <td class="amount">{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="date">{{ $bill->due_date ? date('Y-m-d', strtotime($bill->due_date)) : '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No bills found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>