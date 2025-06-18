<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = ['user_id', 'nama'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->hasOne(Kelas::class, 'walikelas');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}