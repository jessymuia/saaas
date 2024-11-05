{{-- resources/views/pdfs/invoice-details.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            height: auto;
        }
        .company-info {
            float: right;
            text-align: right;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px;
        }
        .billing-info {
            margin-bottom: 30px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .items-table th {
            background-color: #f5f5f5;
            text-align: left;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 5px;
        }
        .totals .grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .payment-info {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
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
    <div class="container">
    <div class="header">
        <h1>Property Details </h1>
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
    </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <td><strong>Invoice #:</strong></td>
                    <td>{{ $invoice->id }}</td>
                    <td><strong>Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Due Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_due_date)->format('d/m/Y') }}</td>
                    <td><strong>Invoice Month:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_for_month)->format('F, Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="billing-info">
            <h3>Bill To:</h3>
            <p>
                <strong>{{ $invoice->tenancyAgreement->tenant->name }}</strong><br>
                Property: {{ $invoice->tenancyAgreement->unit->property->name }}<br>
                Unit: {{ $invoice->tenancyAgreement->unit->name }}
            </p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>VAT</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->tenancyBills as $bill)
                <tr>
                    <td>{{ $bill->name }}</td>
                    <td>KES {{ number_format($bill->amount, 2) }}</td>
                    <td>KES {{ number_format($bill->vat, 2) }}</td>
                    <td>KES {{ number_format($bill->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>KES {{ number_format($subTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>VAT Total:</td>
                    <td>KES {{ number_format($vatTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Total:</td>
                    <td>KES {{ number_format($total, 2) }}</td>
                </tr>
                <tr>
                    <td>Credit Notes:</td>
                    <td>KES {{ number_format($creditNotes, 2) }}</td>
                </tr>
                <tr>
                    <td>Payments:</td>
                    <td>KES {{ number_format($payments, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Balance Due:</td>
                    <td>KES {{ number_format($balance, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

           
    </div>
</body>
</html>