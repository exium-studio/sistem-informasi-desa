<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'documents';

    protected $casts = [
        'uploaded_by' => 'integer',
        'document_status_id' => 'integer',
        'verified_by' => 'integer',
        'created_at' => 'datetime:UTC',
        'updated_at' => 'datetime:UTC',
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
     * Get the document_status that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document_status(): BelongsTo
    {
        return $this->belongsTo(DocumentStatus::class, 'document_status_id', 'id');
    }

    /**
     * Get the verified_user that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verified_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    /**
     * Get the uploaded_user that owns the Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploaded_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
