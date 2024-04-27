<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResource extends JsonResource
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
            'kode' => $this->kode,
            'nama' => $this->nama,
            'job_assignment' => JobAssignmentResource::collection($this->whenLoaded('job_assignment')->loadMissing('job')),
            'jenis_kelamin' => $this->jenis_kelamin,
            'username' => $this->username,
            'role' => $this->role
        ];
    }
}
