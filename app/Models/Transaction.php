<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Nazwa klucza głównego w tabeli.
     *
     * @var string
     */
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
        return $this->belongsTo(Subcategory::class, 'id_subCategory');
    }
}
