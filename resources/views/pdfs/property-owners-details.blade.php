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
            width: 30%;
        }

        .financial-table {
            width: 100%;
            border-collapse: collapse;
        }

        .financial-table th, .financial-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 8pt;
        }

        .amount {
            text-align: right;
            font-family: serif;
        }

        .total-row {
            font-weight: bold;
            background-color: #f3f3f3;
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
                        <p>PROPERTY OWNER DETAILS</p>
                    </div>
                </td>
                <td class="s1" colspan="3"></td>
            </tr>

            <!-- LOGO & ADDRESS -->
            <tr>
                <td class="s1" colspan="3" style="text-align: center;">
                <div style="height: 73px;">
                        <img src="" 
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

            <!-- Property Owner Information -->
            <tr>
                <td colspan="7" class="section-header">Property Owner Information</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $propertyOwner->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $propertyOwner->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number:</th>
                            <td>{{ $propertyOwner->phone_number }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $propertyOwner->address }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Property Information -->
            <tr>
                <td colspan="7" class="section-header">Property Information</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <tr>
                            <th>Property Name:</th>
                            <td>{{ $propertyOwner->property->name }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Financial Summary -->
            <tr>
                <td colspan="7" class="section-header">Financial Summary</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="financial-table">
                        <tr>
                            <th style="width: 70%">Description</th>
                            <th class="amount">Amount (KES)</th>
                        </tr>
                        <tr>
                            <td>Balance Carried Forward</td>
                            <td class="amount">{{ number_format($balanceCarriedForward, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total Invoiced</td>
                            <td class="amount">{{ number_format($totalInvoiced, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total Paid</td>
                            <td class="amount">{{ number_format($totalPaid, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td>Current Balance</td>
                            <td class="amount">{{ number_format($balance, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Recent Invoices -->
            <tr>
                <td colspan="7" class="section-header">Recent Invoices</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th style="width: 15%">Invoice #</th>
                                <th style="width: 25%">Date</th>
                                <th style="width: 25%">Due Date</th>
                                <th style="width: 20%" class="amount">Amount</th>
                                <th style="width: 15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_for_month)->format('M j, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_due_date)->format('M j, Y') }}</td>
                                <td class="amount">{{ number_format($invoice->amount, 2) }}</td>
                                <td>{{ $invoice->invoice_status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>