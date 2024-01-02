<div>
    <p>
        Dear {{$tenantName}},

        I hope this message finds you well. We appreciate your tenancy at {{$unitName.' '.$propertyName}}, and we would like to inform you about an upcoming change in your rent amount.

        In accordance with the terms outlined in your tenancy agreement, we have implemented a rent escalation starting from {{$escalationStartDate}}. The new monthly rent amount will be
        {{$newRentAmount}}.

        Here are the details of the rent escalation:

        Current Rent Amount: {{$oldRentAmount}}
        New Rent Amount: {{$newRentAmount}}
        Effective Date: {{$escalationStartDate}}

        We've generated an invoice that applies upto to {{$escalationEndDate}} .The new rent amount will be reflected in a separate invoice.

        We understand that changes in rent can be impactful, and we are committed to providing you with excellent service and maintaining the quality of your living experience. If you have any questions or concerns regarding this rent increase, please do not hesitate to reach out to us.

        Thank you for your understanding and cooperation.

        Sincerely,
        {{$companyName}}
    </p>
</div>
