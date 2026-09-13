<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'changed_by', 'previous_status', 'status', 'reason'])]
class UserStatusChange extends Model
{
    public function administrator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
