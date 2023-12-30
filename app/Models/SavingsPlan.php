<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
        'id_priority',
        'is_completed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'id_priority');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->translatedFormat('d M Y');
    }

    public function getFormattedEndDateSavingsPlanAttribute()
    {
        return Carbon::parse($this->attributes['end_date_savings_plan'])->translatedFormat('d M Y');
    }
}
