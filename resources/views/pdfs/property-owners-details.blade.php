<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Property Owner Details - {{ $propertyOwner->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            text-align: left;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 20px;
        }
        .property-owner-details {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .property-details {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .financial-summary {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .amount {
            text-align: right;
            font-family: monospace;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .letterhead-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo-container {
            flex: 0 0 300px;
        }
        
        .logo-container img {
            max-width: 100%;
            height: auto;
        }
        
        .contact-details {
            flex: 0 0 400px;
            text-align: right;
        }
        
        .contact-details p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .detail-label {
            font-weight: bold;
        }
        
        .page-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="header">
        <h1>Property Owners Details </h1>
        <!-- <p>Generated on: {{ $timestamp }}</p> -->

        <div class="letterhead-container">
        <div class="logo-container">
        <img src="/app/public/'.$company->logo" style="display: block; max-width: 100%;"  height="73"  alt="Hamud Realtor Logo"/>
        </div>
        <div class="contact-details">
            <p><span class="detail-label">Location:</span>{{ $company->location }}</p>
            <p><span class="detail-label">Address:</span>{{ $company->address }}</p>
            <p><span class="detail-label">Phone:</span>{{ $company->phone_number }}</p>
            <p><span class="detail-label">Email:</span> {{ $company->email }}</p>
        </div>
    </div>
        <!-- {{ $company }}
        <p>
        <img src="url({{ URL::asset('images/hamud_top_doc_logo.png')}})" style="display: block; max-width: 100%;"  height="73"  alt="Hamud Realtor Logo"/>
            {{ $company->name }}
            {{ $company->location }}
            {{ $company->address }}
            {{ $company->email }}
            {{ $company->phone_number }}
        </p> -->
    </div>

    <h1 class="text-center">Property Owner Details</h1>

    <div class="property-owner-details">
        <h3>Property Owner Information</h3>
        <table>
            <tr>
                <th style="width: 30%">Name:</th>
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
    </div>

    <div class="property-details">
        <h3>Property Information</h3>
        <table>
            <tr>
                <th style="width: 30%">Property Name:</th>
                <td>{{ $propertyOwner->property->name }}</td>
            </tr>
        </table>
    </div>

    <div class="financial-summary">
        <h3>Financial Summary</h3>
        <table>
            <tr>
                <th style="width: 70%">Description</th>
                <th class="text-right">Amount (KES)</th>
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
    </div>

    <div class="invoice-list">
        <h3>Recent Invoices</h3>
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th class="text-right">Amount</th>
                    <th>Status</th>
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
    </div>


</body>
</html>