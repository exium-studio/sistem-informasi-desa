<?php

namespace App\Http\Resources\Templates\Response;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithoutDataResource extends JsonResource
{
    public $status;
    public $case;
    public $title;
    public $description;

    public function __construct($status, $case, $title, $description)
    {
        parent::__construct(null);
        $this->status = $status;
        $this->case = $case;
        $this->title = $title;
        $this->description = $description;
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => [
                'title' => $this->title,
                'description' => $this->description,
            ],
            // Tambahan baru, jangan lupa modifikasi dicontroller
            'case' => $this->case
        ];
    }
}
