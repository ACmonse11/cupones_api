<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Coupon;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }
}
