<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefaultAppModel extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected static function boot(): void
    {
        parent::boot();

        // Automatically stamp saas_client_id on every new record when inside
        // a tenant context (single-database multi-tenancy via stancl/tenancy).
        static::creating(function ($model) {
            if (empty($model->saas_client_id) && function_exists('tenancy') && tenancy()->initialized) {
                $model->saas_client_id = tenant()?->id;
            }
        });
    }
//    protected $hidden = ['created_by', 'updated_by', 'deleted_by'];
    protected $casts = [
        'status' => 'boolean',
        'archive' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class,'deleted_by');
    }
}
