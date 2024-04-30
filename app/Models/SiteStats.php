<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteStats extends Model
{
    protected $fillable = ['total_visits', 'total_visits_today', 'total_visits_last_7_days', 'total_uniques', 'bounce_rate'];

    use HasFactory;
}
