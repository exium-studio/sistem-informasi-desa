<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:UTC',
            'updated_at' => 'datetime:UTC',
        ];
    }
}
