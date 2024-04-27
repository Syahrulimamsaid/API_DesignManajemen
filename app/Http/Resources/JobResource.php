<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        // dd($this);
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'nama' => $this->nama,
            'perusahaan' => $this->perusahaan,
            'tanggal_kirim' => date_format(date_create($this->tanggal_kirim), 'Y/m/d'),
            'status_data' => $this->status_data,
            'catatan' => $this->catatan,
            'hasil_design' => $this->hasil_design,
            'data_pendukung' => DataPendukungResource::collection($this->whenLoaded('data_pendukung')),
            'status' => $this->status,
            'tanggapan_customer'=>$this->tanggapan_customer,
            'tanggal_diterima'=>$this->tanggal_diterima
        ];
    }
}
