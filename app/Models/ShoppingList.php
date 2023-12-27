<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ShoppingList extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_shopping_list';

    protected $fillable = [
        'title_shopping_list',
        'description_shopping_list',
        'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function getFormattedUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->translatedFormat('H:i, d M Y');
    }
}
