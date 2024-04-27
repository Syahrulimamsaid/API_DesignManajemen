<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobScheduleResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'job' => $this->whenLoaded('job'),
            'tanggal_pengumpulan' => ($this->tanggal_pengumpulan != null) ? $this->tanggal_pengumpulan : ''
        ];
    }
}
