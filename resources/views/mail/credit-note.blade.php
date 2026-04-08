<div>
    <p>Hello {{ $creditNote->invoice->tenancyAgreement->tenant->name }},</p>
    <p>We hope this email finds you well. We are writing to inform you that you a credit note has been
    issued for your account. Please find the details below:</p>

    <table style="width: 100%;">
        <tr>
            <th>Credit Note Number: </th>
            <td>{{ $creditNote->id }}</td>
        </tr>
        <tr>
            <th>Credit Note Date</th>
            <td>{{ $creditNote->created_at }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>${{ number_format($creditNote->amount_credited, 2) }}</td>
        </tr>
    </table>

    <p>For more details have a look at the included attachment. If you have any questions or concerns regarding this
        credit note, please feel free to contact us. </p>

    <p>Regards,</p>
    <p>{{$companyName}}</p>
</div>
