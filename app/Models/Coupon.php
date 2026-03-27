<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'discount',
        'expiration_date',
        'status',
        'category_id', // 👈 agregado para relación con categorías
        'image',
    ];

    /**
     * Relación: un cupón pertenece a una categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function downloads()
    {
     return $this->hasMany(\App\Models\CouponDownload::class, 'coupon_id');
    }

}
