<!-- resources/views/pdfs/client-details.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Client Details - {{ $client->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #444;
        }
        .client-details {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #f4f4f4;
            padding: 5px 10px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .detail-row {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .value {
            display: inline-block;
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
        <h1>Client Details </h1>
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

    <div class="client-details">
        <div class="section-title">Client Information</div>
        
        <div class="detail-row">
            <span class="label">Name:</span>
            <span class="value">{{ $client->name }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Email:</span>
            <span class="value">{{ $client->email }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Phone Number:</span>
            <span class="value">{{ $client->phone_number }}</span>
        </div>
        
        <div class="detail-row">
            <span class="label">Address:</span>
            <span class="value">{{ $client->address }}</span>
        </div>
        
        @if($client->description)
        <div class="detail-row">
            <span class="label">Description:</span>
            <span class="value">{{ $client->description }}</span>
        </div>
        @endif
    </div>
</body>
</html>