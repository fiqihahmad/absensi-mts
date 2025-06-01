<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';
    protected $fillable = ['tahun_ajaran', 'semester', 'status'];
    public $timestamps = false;

    protected $attributes = [
        'status' => 'Nonaktif'
    ];

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class, 'semester_id');
    }

    public function absensiKelas()
    {
        return $this->hasMany(AbsensiKelas::class, 'semester_id');
    }

}
