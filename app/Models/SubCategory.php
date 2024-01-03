<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_categories';

    use HasFactory;

    protected $primaryKey = 'id_subCategory';

    protected $fillable = [
        'name_subCategory',
        'id_category',
        'id_user',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_subCategory');
    }
}
