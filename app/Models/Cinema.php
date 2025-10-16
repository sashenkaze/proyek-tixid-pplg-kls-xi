<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    //mengaktifkan softdeletes : menghapus tanpa benar2 hilang di db
    use SoftDeletes;

    //mendaftarkan kolom2 yang selain bawaannya, selain id dan timestamps spftdeletes, agar dapat diisi datanya ke column tsb
    protected $fillable = ['name', 'location'];

    //relasi one to many (cinema ke schedule)
    //many di schedule, jd nama fungsi jamak (s)
    public function schedules()
    {
        //panggil jenis relasi
        return $this->hasMany(Schedule::class);
    }
}
