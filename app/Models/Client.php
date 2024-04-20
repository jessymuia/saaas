<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'description',
    ];


    public function manualInvoices()
    {
        return $this->hasMany(ManualInvoices::class);
    }
}
