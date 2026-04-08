<div>
    Hello {{ $tenantName }},<br>
    I hope this email finds you well. Attached, you will find your invoice for the month of {{ $invoiceData['invoice_for_month'] }}.
    Please review the invoice and in case of any concerns, you can reach out to us via email or phone call.<br>

    <table style="width: 100%;">
        <tr>
            <th>Invoice Number</th>
            <td>{{ $invoice->id }}</td>
        </tr>
        <tr>
            <th>Invoice Date</th>
            <td>{{ $invoice->invoice_date }}</td>
        </tr>
        <tr>
            <th>Due Date</th>
            <td>{{ $invoice->invoice_due_date }}</td>
        </tr>
    </table>

    Find attached your invoice for the month of {{ $invoice->invoice_for_month }}.<br>

    Regards,<br>
    {{ $companyName }}
</div>
