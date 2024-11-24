<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt #{{ $payment->id }}</title>
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
        .receipt-title {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            color: #333;
        }
        .receipt-details {
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .receipt-to {
            margin-bottom: 30px;
        }
        .payment-info {
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
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
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

    <h1 class="receipt-title">PAYMENT RECEIPT</h1>

    <div class="receipt-details">
        <table>
            <tr>
                <td><strong>Receipt No:</strong></td>
                <td>#{{ $payment->id }}</td>
                <td><strong>Date:</strong></td>
                <td>{{ $receiptDate }}</td>
            </tr>
            <tr>
                <td><strong>Invoice No:</strong></td>
                <td>#{{ $payment->invoice_id }}</td>
                <td><strong>Payment Date:</strong></td>
                <td>{{ $paymentDate }}</td>
            </tr>
        </table>
    </div>

    <div class="receipt-to">
        <h3>Received From:</h3>
        <p>
            {{ $recipientName }}<br>
            {{ $recipientAddress }}
        </p>
    </div>

    <div class="payment-info">
        <h3>Payment Information</h3>
        <table>
            <tr>
                <th>Payment Method</th>
                <td>{{ $payment->paymentType->type }}</td>
            </tr>
            <tr>
                <th>Paid By</th>
                <td>{{ $payment->paid_by }}</td>
            </tr>
            @if($payment->payment_reference)
            <tr>
                <th>Reference Number</th>
                <td>{{ $payment->payment_reference }}</td>
            </tr>
            @endif
            <tr>
                <th>Amount Paid</th>
                <td class="amount">{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($payment->description)
    <div class="additional-info">
        <h3>Additional Information</h3>
        <p>{{ $payment->description }}</p>
    </div>
    @endif

</body>
</html>