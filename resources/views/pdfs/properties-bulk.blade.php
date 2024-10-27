<!DOCTYPE html>
<html>
<head>
    <title>Properties Report</title>
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
        .property-card {
            margin-bottom: 30px;
            page-break-inside: avoid;
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
            font-size: 11px;
        }
        th {
            background: #f5f5f5;
        }
        .summary-section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Properties Report</h1>
        <p>Generated on: {{ $timestamp }}</p>
        <p>Total Properties: {{ $properties->count() }}</p>
    </div>

    <div class="summary-section">
        <div class="section-title">Summary</div>
        <div class="property-info">
            <div>
                <span class="label">Total Units:</span>
                <span>{{ $properties->sum(fn($property) => $property->number_of_units) }}</span>
            </div>
            <div>
                <span class="label">VATable Properties:</span>
                <span>{{ $properties->where('is_vatable', true)->count() }}</span>
            </div>
        </div>
    </div>

    @foreach($properties as $property)
    <div class="property-card">
        <div class="section">
            <div class="section-title">Property #{{ $property->id }} - {{ $property->name }}</div>
            <div class="property-info">
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
            <div class="section-title">Units Summary</div>
            <table>
                <thead>
                    <tr>
                        <th>Unit Number</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($property->units as $unit)
                    <tr>
                        <td>{{ $unit->unit_number }}</td>
                        <td>{{ $unit->status ? 'Active' : 'Inactive' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
    </div>
    @endforeach
</body>
</html>