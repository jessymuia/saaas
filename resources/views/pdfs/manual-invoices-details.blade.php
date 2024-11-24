<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
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
        }
        .company-logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-to {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .totals {
            text-align: right;
            margin-top: 30px;
        }
        .totals table {
            width: 300px;
            margin-left: auto;
        }
        .payment-details {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
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
        <h1>Manual Invoices Details </h1>
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

    <div class="invoice-details">
        <h1>INVOICE</h1>
        <p><strong>Invoice #:</strong> {{ $invoice->id }}<br>
        <strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}<br>
        <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_due_date)->format('d/m/Y') }}</p>
    </div>

    <div class="invoice-to">
        <h3>Bill To:</h3>
        <p>{{ $recipientName }}<br>
        {{ $recipientAddress }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%">Description</th>
                <th style="width: 20%">Amount</th>
                <th style="width: 20%">VAT</th>
                <th style="width: 20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->manualInvoiceItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ number_format($item->amount, 2) }}</td>
                <td>{{ number_format($item->vat, 2) }}</td>
                <td>{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
            @if($invoice->manualInvoiceItems->count() < 10)
                @for($i = 0; $i < (10 - $invoice->manualInvoiceItems->count()); $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td>{{ number_format($subTotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>VAT Total:</strong></td>
                <td>{{ number_format($vatTotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Payments:</strong></td>
                <td>{{ number_format($payments, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Credit Notes:</strong></td>
                <td>{{ number_format($creditNotes, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Balance Due:</strong></td>
                <td>{{ number_format($balance, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="payment-details">
        <h3>Payment Details:</h3>
        <p><strong>Bank Name:</strong> {{ $company->bank_name }}<br>
        <strong>Account Name:</strong> {{ $company->account_name }}<br>
        <strong>Account Number:</strong> {{ $company->account_number }}<br>
        <strong>Bank Branch:</strong> {{ $company->bank_branch }}<br>
        <strong>Mpesa Paybill:</strong> {{ $company->mpesa_paybill_number }}</p>
    </div>


</body>
</html>