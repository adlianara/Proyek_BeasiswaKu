<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = ['name', 'slug', 'provider', 'level', 'deadline', 'summary', 'description', 'requirements', 'benefits', 'application_url', 'is_active'];
    protected function casts(): array { return ['deadline' => 'date', 'requirements' => 'array', 'benefits' => 'array', 'is_active' => 'boolean']; }
    public function scopeActive(Builder $query): void { $query->where('is_active', true); }
}
