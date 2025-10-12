<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Customer,Supplier,Product,Material,Order,OrderDetail,Invoice,Payment,PurchaseOrder,PurchaseOrderDetail,Formula,FormulaDetail,Production,JournalEntry,Account};

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure some accounts exist (AccountsTableSeeder should create full chart)
        if (Account::count() === 0) {
            $this->call(AccountsTableSeeder::class);
        }

        // Base master data
        $customers = Customer::factory()->count(15)->create();
        $suppliers = Supplier::factory()->count(10)->create();
        $materials = Material::factory()->count(30)->create();

        // Formulas for products
        $formulas = Formula::factory()->count(5)->create()->each(function($formula) use ($materials){
            $pick = $materials->random(rand(2,4));
            foreach ($pick as $mat) {
                FormulaDetail::factory()->create([
                    'formula_id' => $formula->id,
                    'material_id' => $mat->id,
                    'qty' => fake()->randomFloat(2, 0.1, 2)
                ]);
            }
            $formula->calculateTotal();
        });

        // Products (some with formulas)
        $products = Product::factory()->count(20)->create()->each(function($product) use ($formulas){
            if ($formulas->isNotEmpty() && rand(0,1)) {
                $product->update(['formula_id' => $formulas->random()->id]);
            }
        });

        // Purchase Orders with details
        $purchaseOrders = PurchaseOrder::factory()->count(12)->create();
        foreach ($purchaseOrders as $po) {
            $detailCount = rand(1,5);
            $total = 0;
            for ($i=0;$i<$detailCount;$i++) {
                $mat = $materials->random();
                $qty = fake()->randomFloat(2, 1, 50);
                PurchaseOrderDetail::factory()->create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $mat->id,
                    'qty' => $qty,
                ]);
                $total += $qty * $mat->price;
            }
            $po->update(['total' => $total]);
        }

        // Sales Orders with details
        $orders = Order::factory()->count(20)->create();
        foreach ($orders as $order) {
            $detailCount = rand(1,5);
            $total = 0;
            for ($i=0;$i<$detailCount;$i++) {
                $prod = $products->random();
                $qty = fake()->randomFloat(2, 1, 10);
                OrderDetail::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $prod->id,
                    'qty' => $qty,
                ]);
                $total += $qty * $prod->price;
            }
            $order->update(['total' => $total]);
        }

        // Invoices and payments
        $invoices = Invoice::factory()->count(15)->create();
        foreach ($invoices as $inv) {
            // 0-2 payments per invoice
            $payCount = rand(0,2);
            $invTotal = $inv->order?->total ?? 0;
            $paid = 0;
            for ($i=0;$i<$payCount;$i++) {
                $amount = min(fake()->numberBetween(25000, 500000), max($invTotal - $paid, 0));
                if ($amount <= 0) break;
                Payment::factory()->create([
                    'invoice_id' => $inv->id,
                    'amount' => $amount,
                ]);
                $paid += $amount;
            }
        }

        // Productions
        Production::factory()->count(8)->create();

        // Journal Entries: create 10 per transaction type
        $types = ['purchase_order','goods_received','production_process','sales_order','invoice_sent','payment_received','payment_to_supplier'];
        foreach ($types as $type) {
            for ($i=0; $i<10; $i++) {
                // For demo, just attach to any existing transaction id based on type
                $transactionId = 0;
                $transactionName = '';
                switch ($type) {
                    case 'purchase_order':
                        $transactionId = ($purchaseOrders->random()->id) ?? 0;
                        $transactionName = 'purchase_orders';
                        break;
                    case 'goods_received':
                        $transactionId = ($purchaseOrders->random()->id) ?? 0;
                        $transactionName = 'purchase_orders';
                        break;
                    case 'production_process':
                        $transactionId = (Production::inRandomOrder()->value('id')) ?? 0;
                        $transactionName = 'productions';
                        break;
                    case 'sales_order':
                        $transactionId = ($orders->random()->id) ?? 0;
                        $transactionName = 'orders';
                        break;
                    case 'invoice_sent':
                        $transactionId = ($invoices->random()->id) ?? 0;
                        $transactionName = 'invoices';
                        break;
                    case 'payment_received':
                        $transactionId = (Payment::inRandomOrder()->value('id')) ?? 0;
                        $transactionName = 'payments';
                        break;
                    case 'payment_to_supplier':
                        $transactionId = ($purchaseOrders->random()->id) ?? 0;
                        $transactionName = 'purchase_orders';
                        break;
                }
                // Create two lines to balance debit/credit
                $amount = fake()->numberBetween(50000, 2000000);
                $debitAccount = Account::inRandomOrder()->value('id');
                $creditAccount = Account::inRandomOrder()->where('id','<>',$debitAccount)->value('id');
                if(!$debitAccount || !$creditAccount){ continue; }
                JournalEntry::create([
                    'transaction_id' => $transactionId,
                    'transaction_name' => $transactionName,
                    'dt' => fake()->dateTimeBetween('-90 days','now'),
                    'account_id' => $debitAccount,
                    'debit' => $amount,
                    'credit' => 0,
                    'desc' => ucfirst(str_replace('_',' ',$type)).' debit',
                    'journal_entry_id' => null,
                ]);
                JournalEntry::create([
                    'transaction_id' => $transactionId,
                    'transaction_name' => $transactionName,
                    'dt' => fake()->dateTimeBetween('-90 days','now'),
                    'account_id' => $creditAccount,
                    'debit' => 0,
                    'credit' => $amount,
                    'desc' => ucfirst(str_replace('_',' ',$type)).' credit',
                    'journal_entry_id' => null,
                ]);
            }
        }
    }
}
