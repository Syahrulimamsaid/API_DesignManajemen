<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function data_pendukung(){
        return $this->hasMany(DataPendukung::class,'job_kode','kode');
    }
}
