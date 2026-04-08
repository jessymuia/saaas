<?php

namespace App\Auth;

use App\Models\SystemAdmin;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class SystemAdminUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        if (!$this->isValidUuid($identifier)) {
            return null;
        }

        return SystemAdmin::find($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        if (!$this->isValidUuid($identifier)) {
            return null;
        }

        return SystemAdmin::where('id', $identifier)
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
