<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wine extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'description',
        'code',
        'colour',
        'effervescence',
        'sweetness',
        'year'

    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
