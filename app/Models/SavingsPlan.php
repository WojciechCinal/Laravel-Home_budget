<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsPlan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_savings_plan';

    protected $fillable = [
        'name_savings_plan',
        'goal_savings_plan',
        'amount_savings_plan',
        'end_date_savings_plan',
        'id_user',
        'id_priority'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'id_priority');
    }
}
