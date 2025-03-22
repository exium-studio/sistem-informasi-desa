<?php

namespace App\Http\Resources\Web\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillageResource extends JsonResource
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
            'name' => $this->name,
            'summary' => $this->summary,
            'vision' => $this->vision,
            'mission' => $this->mission,
            'village_funds' => $this->village_funds,
            'document_history' => new DocumentResource($this->document_history),
            'document_image' => new DocumentResource($this->document_image),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
