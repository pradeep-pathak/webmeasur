<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pageview extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'path', 'title', 'duration', 'scroll_depth', 'visit_id', 'website_id'];

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }
}
