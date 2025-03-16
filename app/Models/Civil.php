<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Civil extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'civils';

    protected $casts = [
        'user_id' => 'integer',
        'religion_id' => 'integer',
        'education_id' => 'integer',
        'job_type_id' => 'integer',
        'blood_type_id' => 'integer',
        'maried_status_id' => 'integer',
        'relationship_status_id' => 'integer',
        'citizenship_id' => 'integer',
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
     * Get the user that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the religion that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id', 'id');
    }

    /**
     * Get the education that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class, 'education_id', 'id');
    }

    /**
     * Get the job_type that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job_type(): BelongsTo
    {
        return $this->belongsTo(JobType::class, 'job_type_id', 'id');
    }

    /**
     * Get the blood_type that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blood_type(): BelongsTo
    {
        return $this->belongsTo(BloodType::class, 'blood_type_id', 'id');
    }

    /**
     * Get the maried_status that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maried_status(): BelongsTo
    {
        return $this->belongsTo(MariedStatus::class, 'maried_status_id', 'id');
    }

    /**
     * Get the relationship_status that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relationship_status(): BelongsTo
    {
        return $this->belongsTo(RelationshipStatus::class, 'relationship_status_id', 'id');
    }

    /**
     * Get the citizenship that owns the Civil
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function citizenship(): BelongsTo
    {
        return $this->belongsTo(Citizenship::class, 'citizenship_id', 'id');
    }
}
