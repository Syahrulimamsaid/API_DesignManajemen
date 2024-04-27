<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\JobAssignment;
use App\Http\Resources\JobAssignmentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QualityControlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return $this->resource;
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'assignment' => $this->job_assignment,
             new JobAssignmentResource($this->whenLoaded('job_assignment')->loadMissing('job')),
            'petugas' => $this->petugas_kode,
            'komentar' => $this->komentar,
            'status' => $this->status,
            'created_at' => date_format(date_create($this->created_at), 'Y/m/d H:i:s')
        ];
    }
}
