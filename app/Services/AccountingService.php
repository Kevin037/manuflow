<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AccountingService
{
    /**
     * Map of event => lines (each line: direction, account_code)
     * Amount resolution is passed in param; for multi-line with same amount uses same number.
     */
    protected array $rules = [
        'purchase_order' => [ // Raw Material Procurement
            ['dir'=>'debit','code'=>'103001000'], // Raw Material Inventory
            ['dir'=>'credit','code'=>'201002000'], // Supplier Payable
        ],
        'goods_received' => [
            ['dir'=>'debit','code'=>'103002000'], // Work in Process
            ['dir'=>'credit','code'=>'103001000'], // Raw Material Inventory
        ],
        'production_process' => [
            ['dir'=>'debit','code'=>'103003000'], // Finished Goods Inventory
            ['dir'=>'credit','code'=>'103002000'], // Work in Process
        ],
        'sales_order' => [
            ['dir'=>'debit','code'=>'102000000'], // Accounts Receivable
            ['dir'=>'credit','code'=>'401001000'], // Sales Revenue
        ],
        'invoice_sent' => [
            ['dir'=>'debit','code'=>'102000000'], // Accounts Receivable
            ['dir'=>'credit','code'=>'401001000'], // Sales Revenue
            ['dir'=>'debit','code'=>'202001000'], // Output Tax (example – likely should be credit only in real case)
            ['dir'=>'credit','code'=>'202001000'], // Output Tax
        ],
        'payment_received' => [
            ['dir'=>'debit','code'=>'101000000'], // Cash
            ['dir'=>'credit','code'=>'102000000'], // Accounts Receivable
            ['dir'=>'credit','code'=>'202001000'], // Output Tax
        ],
        'payment_to_supplier' => [
            ['dir'=>'debit','code'=>'201002000'], // Supplier Payable
            ['dir'=>'credit','code'=>'101000000'], // Cash
        ],
    ];

    /**
     * Record journal entries for a specific event.
     *
     * @param string $eventKey Key defined in $rules
     * @param int $transactionId Related transaction primary key
     * @param string $transactionName Friendly transaction name (e.g., 'PurchaseOrder')
     * @param float $amount Monetary amount (used for all lines unless customAmounts provided)
     * @param string|null $date Y-m-d date; defaults to today
     * @param string|null $description Description
     * @param array|null $customAmounts Optional array mapping index => amount for heterogeneous lines
     */
    public function record(string $eventKey, int $transactionId, string $transactionName, float $amount, ?string $date = null, ?string $description = null, ?array $customAmounts = null): void
    {
        if(!isset($this->rules[$eventKey])){
            return; // silently ignore unknown events for now
        }
        $dt = $date ?: date('Y-m-d');
        $lines = $this->rules[$eventKey];

        DB::transaction(function() use ($lines, $amount, $customAmounts, $transactionId, $transactionName, $dt, $description){
            foreach($lines as $idx => $line){
                $lineAmount = $customAmounts[$idx] ?? $amount;
                $account = Account::where('code',$line['code'])->first();
                if(!$account){
                    continue; // skip if account missing
                }
                JournalEntry::create([
                    'transaction_id' => $transactionId,
                    'transaction_name' => $transactionName,
                    'dt' => $dt,
                    'account_id' => $account->id,
                    'debit' => $line['dir']==='debit' ? $lineAmount : 0,
                    'credit' => $line['dir']==='credit' ? $lineAmount : 0,
                    'desc' => $description,
                    'journal_entry_id' => null,
                ]);
            }
        });
    }
}
