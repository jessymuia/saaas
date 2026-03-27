<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class TenantUserProvider extends EloquentUserProvider
{
   
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $query = User::withoutTenantScope(); 
        foreach ($credentials as $key => $value) {
            if ($key === 'password') continue;
            $query->where($key, $value);
        }

        return $query->first();
    }

   
    public function retrieveById($identifier): ?Authenticatable
    {
        return User::withoutTenantScope()->find($identifier);
    }

   
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return User::withoutTenantScope()
            ->where('id', $identifier)
            ->where('remember_token', $token)
            ->first();
    }
}