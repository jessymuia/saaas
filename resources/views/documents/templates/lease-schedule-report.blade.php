<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body
        {
            font-family: Arial, sans-serif;
        }
        .header 
        {
            text-align: center; margin-bottom: 30px;
        }
        .company-logo
        { 
            max-width: 200px; margin-bottom: 20px;
        }
        .report-title {
            font-size: 24px; font-weight: bold; margin-bottom: 20px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left; 
        }
        th {
            background-color: #f5f5f5;
        }
        .financial-summary {
            margin-top: 30px;
        }
        .payment-schedule {
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="@#logoUrl" class="company-logo" alt="@#companyName Logo">
        <h1>@#companyName</h1>
        <p>@#companyAddress</p>
        <p>@#companyEmail | @#companyPhoneNumber</p>
        <div class="report-title">Lease Schedule Report</div>
    </div>

    <div class="section">
        <div class="section-title">Lease Information</div>
        <table>
            <tr>
                <th>Agreement ID</th>
                <td>@#tenancyAgreementId</td>
                <th>Tenant Name</th>
                <td>@#tenantName</td>
            </tr>
            <tr>
                <th>Property Name</th>
                <td>@#propertyName</td>
                <th>Unit Description</th>
                <td>@#unitDescription</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>@#startDate</td>
                <th>End Date</th>
                <td>@#endDate</td>
            </tr>
            <tr>
                <th>Duration</th>
                <td colspan="3">@#duration</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Financial Details</div>
        <table>
            <tr>
                <th>Payment Period</th>
                <td>@#paymentPeriod</td>
                <th>Payment Due</th>
                <td>@#paymentDue</td>
            </tr>
            <tr>
                <th>Total Due Amount</th>
                <td>@#totalDueAmount</td>
                <th>Total Paid Amount</th>
                <td>@#totalPaidAmount</td>
            </tr>
            @#escalationDetails
        </table>
    </div>

    <div class="section">
        <div class="section-title">Payment Schedule</div>
        <table>
            <thead>
                <tr>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @#paymentScheduleHTML
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Payment Summary</div>
        <table>
            <tr>
                <th>Payments Made</th>
                <td>@#paymentsMade</td>
            </tr>
            <tr>
                <th>Payments Overdue</th>
                <td>@#paymentsOverdue</td>
            </tr>
            <tr>
                <th>Upcoming Payments</th>
                <td>@#upcomingPayments</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on @#generatedDate
    </div>
</body>
</html>