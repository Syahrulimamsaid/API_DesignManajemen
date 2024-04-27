<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function job()
    {
        return $this->belongsTo(Job::class,'job_kode','kode');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'designer_kode', 'kode');
    }
}
