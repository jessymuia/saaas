<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_monthly',   
        'limits',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'limits'        => 'array',
        ];
    }

    public function saasClients(): HasMany
    {
        return $this->hasMany(SaasClient::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }
}