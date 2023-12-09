<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    /**
     * Nazwa klucza głównego w tabeli.
     *
     * @var string
     */
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
}
