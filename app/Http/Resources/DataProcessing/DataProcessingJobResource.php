<?php

namespace App\Http\Resources\DataProcessing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataProcessingJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'download_url' => $this->isCompleted() && $this->file_path ? url("storage/{$this->file_path}") : null,
        ];
    }
}
