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

        .s1 {
            text-align: left;
            color: #000;
            font-family: serif;
            font-size: 10pt;
            vertical-align: middle;
            direction: ltr;
            padding: 2px 3px;
        }

        .s2 {
            text-align: center;
            font-weight: bold;
            color: #000;
            font-family: serif;
            font-size: 11pt;
            vertical-align: middle;
            direction: ltr;
            padding: 2px 3px;
            letter-spacing: 0.5px;
        }

        .s3 {
            text-align: right;
            color: #000;
            font-family: serif;
            font-size: 9pt;
            vertical-align: middle;
            direction: ltr;
            padding: 2px 3px;
        }

        .s11 {
            border-bottom: 1px SOLID #000;
            background-color: #f3f3f3;
            text-align: center;
            font-weight: bold;
            color: #000;
            font-family: serif;
            font-size: 11pt;
            vertical-align: middle;
            padding: 2px 3px;
            line-height: 180%;
        }

        .s12b {
            border: 1px SOLID #000;
            text-align: left;
            font-weight: bold;
            color: #000;
            font-family: serif;
            font-size: 8pt;
            vertical-align: middle;
            padding: 2px 3px;
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

        .section-header {
            background-color: #f3f3f3;
            font-weight: bold;
            padding: 8px;
            border: 1px SOLID #000;
            font-size: 9pt;
        }

        table.details-table {
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
        }

        @media print {
            td {
                padding-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="waffle">
        <!-- TITLE -->
        <table cellspacing="0" cellpadding="0" width="100%">
            <tr style="height: 20px;">
                <td class="s1" colspan="3"></td>
                <td class="s2" colspan="1">
                    <div style="text-align: center;">
                        <p>PROPERTY DETAILS</p>
                    </div>
                </td>
                <td class="s1" colspan="3"></td>
            </tr>

            <!-- LOGO & ADDRESS -->
            <tr>
                <td class="s1" colspan="3" style="text-align: center;">
                    <div style="height: 73px;">
                        <img src="{{ storage_path('app/public/'.$company->logo) }}" style="display: block; margin: auto; max-height: 73px;" alt="Company Logo"/>
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

            <!-- Property Information -->
            <tr>
                <td colspan="7" class="section-header">Property Information</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <p style="margin: 5px 0"><strong>Property Name:</strong> {{ $property->name }}</p>
                    <p style="margin: 5px 0"><strong>Address:</strong> {{ $property->address }}</p>
                    <p style="margin: 5px 0"><strong>Property Type:</strong> {{ $property->propertyType->type }}</p>
                    <p style="margin: 5px 0"><strong>Number of Units:</strong> {{ $property->number_of_units }}</p>
                    <p style="margin: 5px 0"><strong>VAT Status:</strong> {{ $property->is_vatable ? 'VATable' : 'Non-VATable' }}</p>
                </td>
            </tr>

            <!-- Units Section -->
            @if($property->units->count() > 0)
            <tr>
                <td colspan="7" class="section-header">Units ({{ $property->units->count() }})</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Unit Name</th>
                                <th>Status</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($property->units as $unit)
                            <tr>
                                <td>Unit {{ $unit->name }}</td>
                                <td>{{ $unit->status ? 'Active' : 'Inactive' }}</td>
                                <td>{{ $unit->description ?: 'No description available' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

            <!-- Utilities Section -->
            @if($property->utilities->count() > 0)
            <tr>
                <td colspan="7" class="section-header">Utilities</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Utility Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($property->utilities as $utility)
                            <tr>
                                <td>{{ $utility->utility->name }}</td>
                                <td>{{ $utility->status ? 'Active' : 'Inactive' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

            <!-- Property Services Section -->
            @if($property->propertyServices->count() > 0)
            <tr>
                <td colspan="7" class="section-header">Property Services</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($property->propertyServices as $service)
                            <tr>
                                <td>{{ $service->service->name }}</td>
                                <td>{{ $service->status ? 'Active' : 'Inactive' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

            <!-- Property Payment Details Section -->
            @if($property->propertyPaymentDetails->count() > 0)
            <tr>
                <td colspan="7" class="section-header">Payment Details</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Account Details</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($property->propertyPaymentDetails as $payment)
                            <tr>
                                <td>M-Pesa Paybill: {{ $payment->mpesa_paybill_number }}</td>
                                <td>{{ $payment->account_name .' '. $payment->account_number }}</td>
                                <td>{{ $payment->status ? 'Active' : 'Inactive' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

            <!-- Property Owners Section -->
            @if($property->propertyOwners->count() > 0)
            <tr>
                <td colspan="7" class="section-header">Property Owners</td>
            </tr>
            <tr>
                <td colspan="7" class="section-content">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($property->propertyOwners as $owner)
                            <tr>
                                <td>{{ $owner->name }}</td>
                                <td>{{ $owner->phone_number }}</td>
                                <td>{{ $owner->status ? 'Active' : 'Inactive' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

        </table>
    </div>
</body>
</html>
