<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-section img {
            max-height: 73px;
        }
        .company-info {
            text-align: right;
            margin-bottom: 20px;
        }
        .divider {
            border-bottom: 1px solid #000;
            margin: 20px 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f3f3f3;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .detail-row {
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>CLIENT DETAILS</h2>
        </div>

        <div class="logo-section">
            <img src="" alt="Company Logo">
        </div>

        <div class="company-info">
            <p><strong>Location:</strong> {{ $company->location }}</p>
            <p><strong>Address:</strong> {{ $company->address }}</p>
            <p><strong>Phone:</strong> {{ $company->phone_number }}</p>
            <p><strong>Email:</strong> {{ $company->email }}</p>
        </div>

        <div class="divider"></div>

        <div class="section">
            <div class="section-title">Client Information</div>
            
            <div class="detail-row">
                <span class="label">Name:</span>
                <span>{{ $client->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Email:</span>
                <span>{{ $client->email }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Phone Number:</span>
                <span>{{ $client->phone_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Address:</span>
                <span>{{ $client->address }}</span>
            </div>
            
            @if($client->description)
            <div class="detail-row">
                <span class="label">Description:</span>
                <span>{{ $client->description }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

       
    </div>
</body>
</html>