<?php

namespace App\Models;

use App\Traits\HasArrayRelations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes, HasArrayRelations;

    protected $table = 'announcements';

    protected $guarded = ['id'];

    protected $appends = ['documents'];

    protected $casts = [
        'created_by' => 'integer',
        'document_id' => 'array',
        'location' => 'array',
        'published_at' => 'datetime:UTC',
        'expires_at' => 'datetime:UTC',
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

    public function getDocumentsAttribute()
    {
        return $this->resolveArrayRelation(
            $this->document_id,
            Document::class,
            ['document_status', 'uploaded_user', 'verified_user']
        );
    }

    /**
     * Get the user that owns the Announcement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
