<div>
    Hello {{ $invoicePayment->tenant->name }},<br>
    I hope this email finds you well. Thank your recent payment of KES {{$invoicePayment->amount}}
    for invoice #{{ $invoicePayment->invoice->id }}.<br>

    The payment has been successfully processed. If you have any questions, please feel free to contact us.<br>
    Thank you for choosing us.<br>

    Regards,<br>
    {{ $companyName }}
</div>
