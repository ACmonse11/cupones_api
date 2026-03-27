<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponDownload extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'downloaded_at'
    ];

    // 🔥 IMPORTANTE: usar tu tabla real
    protected $table = 'coupon_user_downloads';
}
