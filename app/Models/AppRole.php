<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role;

class AppRole extends Role implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;
    protected $keyType   = 'string';
}
