<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'qr_code_id',
        'ip',
        'country',
        'city',
        'user_agent',
        'device',
        'browser',
        'referer',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }
}
