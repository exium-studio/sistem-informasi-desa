<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Web\Gens\ExpenseCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpensesResource extends JsonResource
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
            'expense_category' => new ExpenseCategoryResource($this->expense_category),
            'value' => $this->value,
            'description' => $this->description,
            'realization_date' => $this->realization_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
