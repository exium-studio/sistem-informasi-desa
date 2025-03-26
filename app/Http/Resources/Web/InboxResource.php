<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Web\Static\InboxTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InboxResource extends JsonResource
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
            'created_by' => new UserResource($this->created_user),
            'received_by' => $this->received_user,
            'inbox_type' => new InboxTypeResource($this->inbox_type),
            'message' => $this->message,
            'is_verified' => $this->is_verified,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
