<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControl extends Model
{
    use HasFactory;

    protected $guarded =[];
 
    public function job_assignment(){
        return $this->belongsTo(JobAssignment::class,'job_assignment_kode','kode');
    }

    public function petugas(){
        return $this->belongsTo(User::class,'petugas_kode','kode');
    }
}
