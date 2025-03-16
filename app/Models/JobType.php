<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'job_types';

    protected $casts = [
        'created_at' => 'datetime:UTC',
        'updated_at' => 'datetime:UTC',
        'delete_at' => 'datetime:UTC',
    ];

    /**
     * Pastikan waktu selalu dalam UTC saat diambil atau disimpan
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->setTimezone('UTC'),
            set: fn($value) => Carbon::parse($value)->setTimezone('UTC')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->setTimezone('UTC'),
            set: fn($value) => Carbon::parse($value)->setTimezone('UTC')
        );
    }
}
