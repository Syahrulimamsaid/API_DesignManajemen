<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobAssignmentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'designer' => $this->whenLoaded('user'),
            'job' => new JobResource($this->whenLoaded('job')->loadMissing('data_pendukung')),
            'tanggal_pengumpulan' => date_format(date_create($this->tanggal_pengumpulan), 'Y/m/d'),
            'status' => $this->status,
            'qc'=>$this->qc
        ];
    }
}
