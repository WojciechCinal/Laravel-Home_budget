<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = ['name_priority'];

    public function savingsPlans()
    {
        return $this->hasMany(SavingsPlan::class);
    }
}
