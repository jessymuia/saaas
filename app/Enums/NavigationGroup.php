<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case References         = 'References';
    case AccessManagement   = 'Access Management';
    case TenancyManagement  = 'Tenancy Management';
    case Accounting         = 'Accounting';
}