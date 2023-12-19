<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_role';

    protected $fillable = ['name_role'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
