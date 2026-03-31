<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
    'name',
    'slug',
    'description',
    'price',
    'currency',
    'trial_days',
    'billing_cycle',
    'max_properties',
    'max_tenants',
    'max_users',
    'features',
    'is_active',
];

protected $casts = [
    'price' => 'decimal:2',
    'features' => 'array',
    'is_active' => 'boolean',
];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}