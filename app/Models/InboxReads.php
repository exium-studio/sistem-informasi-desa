<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboxReads extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'inbox_reads';

    protected $casts = [
        'inbox_id' => 'integer',
        'user_id' => 'integer',
        'is_read' => 'boolean',
        'read_at' => 'datetime:UTC',
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
     * Get the created_inbox that owns the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_inbox(): BelongsTo
    {
        return $this->belongsTo(Inbox::class, 'inbox_id', 'id');
    }

    /**
     * Get the user_reading that owns the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_reading(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
