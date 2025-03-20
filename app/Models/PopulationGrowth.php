<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopulationGrowth extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'population_growths';

    protected $guarded = ['id'];

    protected $casts = [
        'citizen_total' => 'integer',
        'new_citizen_total' => 'integer',
        'leave_citizen_total' => 'integer',
        'year' => 'integer',
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

    /**
     * Ambil semua user dengan account_status = 2
     */
    public static function getUsersWithActiveStatus()
    {
        return User::where('account_status', 2)->get();
    }

    /**
     * Ambil semua user yang register_at sebelum tahun ini
     */
    public static function getUsersRegisteredBeforeThisYear()
    {
        $startOfYear = Carbon::now()->startOfYear(); // 1 Januari tahun ini
        return User::where('register_at', '<', $startOfYear)->get();
    }

    /**
     * Ambil semua user yang deactivate_at sebelum tahun ini
     */
    public static function getUsersDeactivatedBeforeThisYear()
    {
        $startOfYear = Carbon::now()->startOfYear(); // 1 Januari tahun ini
        return User::where(function ($query) use ($startOfYear) {
            $query->where('deactivate_at', '<', $startOfYear)
                ->orWhereNotNull('deactivate_at');
        })->get();
    }
}
