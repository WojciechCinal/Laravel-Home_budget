<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_transaction';

    protected $fillable = [
        'name_transaction',
        'amount_transaction',
        'date_transaction',
        'id_user',
        'id_category',
        'id_subCategory'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'id_subCategory');
    }

    public function getFormattedDateTransactionAttribute()
    {
        return Carbon::parse($this->attributes['date_transaction'])->translatedFormat('d M Y');
    }
}
