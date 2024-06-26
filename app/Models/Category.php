<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_category';

    protected $fillable = [
        'name_category',
        'id_user',
        'is_active',
        'name_start',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'id_category');
    }

    public function activeSubcategoriesCount()
    {
        return $this->subcategories()->where('is_active', true)->count();
    }

}
