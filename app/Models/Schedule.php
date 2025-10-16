<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['cinema_id', 'movie_id', 'hours', 'price'];

    protected function casts(): array
    {
        return [
            //agar format data yang disimpan array, bukan json
            'hours' => 'array'
        ];
    }

    public function cinema()
    {
        // karena schedule ada fk milik cinema, maka pakai belongsTo
        return $this->belongsTo(Cinema::class, 'cinema_id', 'id');
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }
}
