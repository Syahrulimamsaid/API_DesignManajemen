<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLines extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    public function job_assignment()
    {
        return $this->belongsTo(JobAssignment::class, 'job_assignment_kode', 'kode');
    }

    public function quality_control()
    {
        return $this->belongsTo(QualityControl::class, 'quality_control_kode', 'kode');
    }
}
