<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name_category', 'id_user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}