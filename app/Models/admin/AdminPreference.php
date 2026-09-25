<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminPreference extends Model
{
    protected $fillable = ['user_id', 'notification_preferences'];
    protected function casts(): array { return ['notification_preferences' => 'array']; }
}
