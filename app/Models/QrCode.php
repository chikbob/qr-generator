<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'image_path',
        'size',
        'color_dark',
        'color_light',
        'is_dynamic',
        'slug',
    ];

    public function scans()
    {
        return $this->hasMany(\App\Models\QrScan::class);
    }

}
