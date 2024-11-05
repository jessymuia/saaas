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
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
            text-align: center;
        }
        .timestamp {
            text-align: right;
            font-size: 0.8em;
            margin-bottom: 20px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 0.9em;
        }
        th {
            background-color: #f5f5f5;
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
        <h1>Tenants Details </h1>
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
                <td>{{ $tenant->status ? 'Active' : 'Inactive' }}</td>
            </tr>
        </table>
    </div>

    @if($tenant->tenancyAgreements->count() > 0)
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
                @foreach($tenant->tenancyAgreements as $agreement)
                <tr>
                    <td>{{ $agreement->id }}</td>
                    <td>{{ $agreement->start_date }}</td>
                    <td>{{ $agreement->end_date }}</td>
                    <td>{{ $agreement->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($tenant->tenancyBills->count() > 0)
    <div class="section">
        <div class="section-title">Tenancy Bills</div>
        <table>
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenant->tenancyBills as $bill)
                <tr>
                    <td>{{ $bill->id }}</td>
                    <td>{{ $bill->amount }}</td>
                    <td>{{ $bill->due_date }}</td>
                    <td>{{ $bill->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($tenant->invoices->count() > 0)
    <div class="section">
        <div class="section-title">Invoices</div>
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenant->invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->amount }}</td>
                    <td>{{ $invoice->invoice_date }}</td>
                    <td>{{ $invoice->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($tenant->invoicePayments->count() > 0)
    <div class="section">
        <div class="section-title">Invoice Payments</div>
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenant->invoicePayments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ $payment->payment_method }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>