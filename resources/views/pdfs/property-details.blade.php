<!DOCTYPE html>
<html>
<head>
    <title>Property Details - {{ $property->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            background: #eee;
            padding: 5px;
        }
        .property-info {
            display: table;
            width: 100%;
        }
        .property-info div {
            display: table-row;
        }
        .property-info span {
            display: table-cell;
            padding: 3px;
        }
        .label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
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
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        .unit-number {
            font-weight: bold;
            color: #333;
        }
        .inactive-row {
            background-color: #f9f9f9;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Property Details </h1>
        <!-- <p>Generated on: {{ $timestamp }}</p> -->

        <div class="letterhead-container">
        <div class="logo-container">
        <img src="{{ $company -> logoUrl }}" style="display: block; max-width: 100%;"  height="73"  alt="Hamud Realtor Logo"/>
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

    <div class="section">
        <div class="section-title"> Property Information</div>
        <div class="property-info">
            <div>
                <span class="label">Property Name:</span>
                <span>{{ $property->name }}</span>
            </div>
            <div>
                <span class="label">Address:</span>
                <span>{{ $property->address }}</span>
            </div>
            <div>
                <span class="label">Property Type:</span>
                <span>{{ $property->propertyType->type }}</span>
            </div>
            <div>
                <span class="label">Number of Units:</span>
                <span>{{ $property->number_of_units }}</span>
            </div>
            <div>
                <span class="label">VAT Status:</span>
                <span>{{ $property->is_vatable ? 'VATable' : 'Non-VATable' }}</span>
            </div>
        </div>
    </div>

    @if($property->units->count() > 0)
    <div class="section">
        <div class="section-title">Units ({{ $property->units->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Unit Name</th>
                    <th>Status</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($property->units as $unit)
                <tr class="{{ !$unit->status ? 'inactive-row' : '' }}">
                    <td>
                        Unit {{ $unit->name }}
                    </td>
                    <td>
                        @if($unit->status)
                            <strong>Active</strong>
                        @else
                            Inactive
                        @endif
                    </td>
                    <td>
                        @if(!empty($unit->description))
                            {{ $unit->description }}
                        @else
                            <em>No description available</em>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <div class="section-title">Units</div>
        <div class="empty-state">
            No units have been added to this property yet.
        </div>
    </div>
    @endif

    @if($property->utilities->count() > 0)
    <div class="section">
        <div class="section-title">Utilities</div>
        <table>
            <thead>
                <tr>
                    <th>Utility Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($property->utilities as $utility)
                <tr>
                    <td>{{ $utility->utility_type }}</td>
                    <td>{{ $utility->status ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($property->propertyServices->count() > 0)
    <div class="section">
        <div class="section-title">Property Services</div>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($property->propertyServices as $service)
                <tr>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ $service->status ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>