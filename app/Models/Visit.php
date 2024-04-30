<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'user_signature', 'entry_page', 'referrer', 'device', 'browser', 'os', 'country', 'country_code', 'region', 'city'
    ];

    public function pageviews()
    {
        return $this->hasMany(Pageview::class, 'visit_id');
    }
}
