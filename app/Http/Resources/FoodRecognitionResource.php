<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodRecognitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payload = [
            'success' => (bool) ($this->resource['success'] ?? false),
            'history_id' => $this->resource['history']->id,
            'remaining_requests' => $this->resource['remaining_requests'],
            'data' => $this->resource['api_data'],
        ];

        if (!empty($this->resource['message'])) {
            $payload['message'] = $this->resource['message'];
        }

        return $payload;
    }
}
