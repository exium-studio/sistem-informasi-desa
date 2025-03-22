<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_id' => $this->file_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_mime_type' => $this->file_mime_type,
            'file_size' => $this->file_size,
            'reason' => $this->reason,

            // Relasi dengan user
            'uploaded_by' => new UserResource($this->uploaded_user),
            'document_status' => $this->document_status,
            'verified_by' => $this->verified_user ? new UserResource($this->verified_user) : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
