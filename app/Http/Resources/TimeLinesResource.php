<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\RevisionsResource;
use App\Http\Resources\JobAssignmentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TimeLinesResource extends  JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => $this->event,
            'tanggal_event' => $this->tanggal_event,
            'job_assignment' => new JobAssignmentResource($this->whenLoaded('job_assignment')->loadMissing('job')),
            // 'quality_control' => ($this->quality_control != null) ? new QualityControlResource($this->whenLoaded('quality_control')) : null
            'quality_control'=> $this->quality_control
        ];
    }
}
