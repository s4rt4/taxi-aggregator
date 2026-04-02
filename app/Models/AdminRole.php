<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $fillable = ['name', 'slug', 'is_system', 'description', 'permissions'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class, 'admin_role_id');
    }

    public function hasPermission(string $permission): bool
    {
        // Super Admin has ALL permissions
        if ($this->slug === 'super-admin') {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }
}
