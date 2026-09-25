<?php

namespace App\Console\Commands;

use App\Models\Admin\LedgerEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyLedger extends Command
{
    protected $signature = 'likhae:verify-ledger';
    protected $description = 'Verify ledger idempotency and print account balances';

    public function handle(): int
    {
        $duplicates = LedgerEntry::query()->select('account_type','account_id','type','reference_type','reference_id',DB::raw('COUNT(*) total'))
            ->groupBy('account_type','account_id','type','reference_type','reference_id')->having('total','>',1)->count();
        if ($duplicates) { $this->error("Ledger has {$duplicates} duplicate reference groups."); return self::FAILURE; }
        $rows = LedgerEntry::query()->select('account_type','account_id',DB::raw('SUM(amount_minor) balance_minor'))
            ->groupBy('account_type','account_id')->orderBy('account_type')->orderBy('account_id')->get();
        $this->table(['Account type','Account ID','Balance minor'], $rows->map(fn ($row) => [$row->account_type,$row->account_id,$row->balance_minor]));
        $this->info('Ledger verification passed.');
        return self::SUCCESS;
    }
}
