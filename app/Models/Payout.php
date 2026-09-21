<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = ['seller_id', 'amount_minor', 'status', 'reference', 'decided_by', 'decided_at', 'reversal_ledger_entry_id'];
    protected function casts(): array { return ['decided_at' => 'datetime']; }
    public function seller(): BelongsTo { return $this->belongsTo(Seller::class); }
}
