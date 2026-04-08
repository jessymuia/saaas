<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            padding-top: 0 !important;
            margin-top: 0 !important;
            font-family: serif;
        }

        .waffle {
            overflow: hidden;
            table-layout: fixed;
            width: 100%;
        }

        .s1, .s2, .s3, .s11 {
            font-family: serif;
            direction: ltr;
        }

        .s1 {
            text-align: left;
            color: #000;
            font-size: 10pt;
            vertical-align: middle;
            padding: 2px 3px;
        }

        .s2 {
            text-align: center;
            font-weight: bold;
            color: #000;
            font-size: 11pt;
            vertical-align: middle;
            padding: 2px 3px;
            letter-spacing: 0.5px;
        }

        .s3 {
            text-align: right;
            color: #000;
            font-size: 9pt;
            vertical-align: middle;
            padding: 2px 3px;
        }

        .s11 {
            border-bottom: 1px SOLID #000;
            background-color: #f3f3f3;
            text-align: center;
            font-weight: bold;
            color: #000;
            font-size: 11pt;
            vertical-align: middle;
            padding: 2px 3px;
            line-height: 180%;
        }

        .section-header {
            background-color: #f3f3f3;
            font-weight: bold;
            padding: 8px;
            border: 1px SOLID #000;
            font-size: 9pt;
        }

        .section-content {
            border: 1px SOLID #000;
            text-align: left;
            color: #000;
            font-family: serif;
            font-size: 8pt;
            vertical-align: middle;
            padding: 8px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .details-table th, .details-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 8pt;
        }

        .details-table th {
            background-color: #f3f3f3;
            font-weight: bold;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 4px;
            border-radius: 2px;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 4px;
            border-radius: 2px;
        }

        .amount {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="waffle">
        <table cellspacing="0" cellpadding="0" width="100%">
            <!-- TITLE -->
            <tr style="height: 20px;">
                <td class="s1" colspan="3"></td>
                <td class="s2" colspan="1">
                    <div style="text-align: center;">
                        <p>TENANT DETAILS</p>
                    </div>
                </td>
                <td class="s1" colspan="3"></td>
            </tr>

            <!-- LOGO & ADDRESS -->
            <tr>
                <td class="s1" colspan="3" style="text-align: center;">
                    <div style="height: 73px;">
                        <img src="{{storage_path('app/public/'.$company->logo)}}"
                             style="display: block; margin: auto; max-height: 73px;"
                             alt="Company Logo"/>
                    </div>
                </td>
                <td class="s1"></td>
                <td class="s3" colspan="3">
                    <div>
                        <p style="margin: 0"><span style="font-weight: bold">Location:</span> {{ $company->location }}</p>
                        <p style="margin: 0"><span style="font-weight: bold">Address:</span> {{ $company->address }}</p>
                        <p style="margin: 0"><span style="font-weight: bold">Phone:</span> {{ $company->phone_number }}</p>
                        <p style="margin: 0"><span style="font-weight: bold">Email:</span> {{ $company->email }}</p>
                    </div>
                </td>
            </tr>

            <!-- Separator -->
            <tr style="height: 30px">
                <td class="s11" colspan="7"></td>
            </tr>

            <!-- Tenant Information -->
            <tr>
                <td colspan="7" class="section-header">Tenant Information</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <p style="margin: 5px 0"><strong>Name:</strong> {{ $tenant->name }}</p>
                    <p style="margin: 5px 0"><strong>Email:</strong> {{ $tenant->email }}</p>
                    <p style="margin: 5px 0"><strong>Phone Number:</strong> {{ $tenant->phone_number }}</p>
                    <p style="margin: 5px 0">
                        <strong>Status:</strong>
                        <span class="status-{{ strtolower($tenant->tenancy_status) }}">
                            {{ $tenant->tenancy_status }}
                        </span>
                    </p>
                </td>
            </tr>

            <!-- Tenancy Agreements -->
            <tr>
                <td colspan="7" class="section-header">Tenancy Agreements</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
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
                                    <span class="status-{{ strtolower($agreementStatus) }}">
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
                </td>
            </tr>

            <!-- Tenancy Bills -->
            <tr>
                <td colspan="7" class="section-header">Tenancy Bills</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
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
                                <td>{{ $bill->bill_date ? date('Y-m-d', strtotime($bill->bill_date)) : '' }}</td>
                                <td>{{ $bill->name }}</td>
                                <td class="amount">{{ number_format($bill->total_amount, 2) }}</td>
                                <td>{{ $bill->due_date ? date('Y-m-d', strtotime($bill->due_date)) : '' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">No bills found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>


        </table>
    </div>
</body>
</html>
