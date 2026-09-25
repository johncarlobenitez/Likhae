<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminPolicy extends Model
{
    protected $fillable = ['title', 'slug', 'revision', 'body', 'status', 'published_at', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
