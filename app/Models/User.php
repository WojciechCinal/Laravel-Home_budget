<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'monthly_budget',
        'id_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class, 'id_user');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_user');
    }

    public function savingsPlans()
    {
        return $this->hasMany(SavingsPlan::class, 'id_user');
    }

    public function subcat()
    {
        return $this->hasMany(SubCategory::class, 'id_user');
    }
    public function cat()
    {
        return $this->hasMany(Category::class, 'id_user');
    }
}
