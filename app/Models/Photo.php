<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'wine_id',
        'extension'
    ];

    public function wine()
    {
        return $this->belongsTo(Wine::class);
    }
}
