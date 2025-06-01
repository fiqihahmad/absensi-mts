<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $fillable = ['nis', 'nama', 'jk', 'kelas_id'];
    public $timestamps = false;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class, 'siswa_id');
    }

    public function absensiKelas()
    {
        return $this->hasMany(AbsensiKelas::class, 'siswa_id');
    }
}
