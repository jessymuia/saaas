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
        if (!$this->isValidUuid($identifier)) {
            return null;
        }

        return User::withoutTenantScope()->find($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        if (!$this->isValidUuid($identifier)) {
            return null;
        }

        return User::withoutTenantScope()
            ->where('id', $identifier)
            ->where('remember_token', $token)
            ->first();
    }

    private function isValidUuid(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $value
        );
    }
}