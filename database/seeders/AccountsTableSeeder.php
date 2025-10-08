<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsTableSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code'=>'100000000','name'=>'Aktiva','parent_code'=>null],
            ['code'=>'101000000','name'=>'Kas','parent_code'=>'100000000'],
            ['code'=>'101001000','name'=>'Kas di Bank BCA','parent_code'=>'101000000'],
            ['code'=>'101002000','name'=>'Kas di Bank Mandiri','parent_code'=>'101000000'],
            ['code'=>'102000000','name'=>'Piutang Usaha','parent_code'=>'100000000'],
            ['code'=>'102001000','name'=>'Piutang Dagang','parent_code'=>'102000000'],
            ['code'=>'102002000','name'=>'Piutang Lainnya','parent_code'=>'102000000'],
            ['code'=>'103000000','name'=>'Persediaan','parent_code'=>'100000000'],
            ['code'=>'103001000','name'=>'Persediaan Barang Dagang','parent_code'=>'103000000'],
            ['code'=>'103002000','name'=>'Persediaan Barang Bahan','parent_code'=>'103000000'],
            ['code'=>'104000000','name'=>'Investasi','parent_code'=>'100000000'],
            ['code'=>'105000000','name'=>'Aset Tetap','parent_code'=>'100000000'],
            ['code'=>'105001000','name'=>'Tanah','parent_code'=>'105000000'],
            ['code'=>'105002000','name'=>'Bangunan dan Gedung','parent_code'=>'105000000'],
            ['code'=>'105003000','name'=>'Kendaraan','parent_code'=>'105000000'],
            ['code'=>'106000000','name'=>'Aset Tak Berwujud','parent_code'=>'100000000'],
            ['code'=>'110000000','name'=>'Akumulasi Penyusutan','parent_code'=>'100000000'],
            ['code'=>'200000000','name'=>'Kewajiban','parent_code'=>null],
            ['code'=>'201000000','name'=>'Utang Dagang','parent_code'=>'200000000'],
            ['code'=>'201001000','name'=>'Utang Usaha','parent_code'=>'201000000'],
            ['code'=>'201002000','name'=>'Utang Bank','parent_code'=>'200000000'],
            ['code'=>'202000000','name'=>'Utang Pajak','parent_code'=>'200000000'],
            ['code'=>'202001000','name'=>'PPN Keluaran','parent_code'=>'202000000'],
            ['code'=>'202002000','name'=>'PPN Masukan','parent_code'=>'202000000'],
            ['code'=>'202003000','name'=>'Pajak Penghasilan','parent_code'=>'202000000'],
            ['code'=>'203000000','name'=>'Utang Lainnya','parent_code'=>'200000000'],
            ['code'=>'300000000','name'=>'Modal','parent_code'=>null],
            ['code'=>'301000000','name'=>'Modal Pemilik','parent_code'=>'300000000'],
            ['code'=>'301001000','name'=>'Modal Disetor','parent_code'=>'301000000'],
            ['code'=>'302000000','name'=>'Laba Ditahan','parent_code'=>'300000000'],
            ['code'=>'400000000','name'=>'Pendapatan','parent_code'=>null],
            ['code'=>'401000000','name'=>'Pendapatan Penjualan','parent_code'=>'400000000'],
            ['code'=>'401001000','name'=>'Pendapatan Jasa','parent_code'=>'400000000'],
            ['code'=>'401002000','name'=>'Pendapatan Produk','parent_code'=>'400000000'],
            ['code'=>'402000000','name'=>'Pendapatan Lainnya','parent_code'=>'400000000'],
            ['code'=>'500000000','name'=>'Biaya','parent_code'=>null],
            ['code'=>'501000000','name'=>'Biaya Operasional','parent_code'=>'500000000'],
            ['code'=>'501001000','name'=>'Biaya Gaji','parent_code'=>'501000000'],
            ['code'=>'501002000','name'=>'Biaya Sewa','parent_code'=>'501000000'],
            ['code'=>'501003000','name'=>'Biaya Transportasi','parent_code'=>'501000000'],
            ['code'=>'501004000','name'=>'Biaya Perjalanan Dinas','parent_code'=>'501000000'],
            ['code'=>'502000000','name'=>'Biaya Pemeliharaan Aset','parent_code'=>'500000000'],
            ['code'=>'503000000','name'=>'Biaya Pemasaran','parent_code'=>'500000000'],
            ['code'=>'503001000','name'=>'Biaya Iklan','parent_code'=>'503000000'],
            ['code'=>'503002000','name'=>'Biaya Promosi','parent_code'=>'503000000'],
            ['code'=>'504000000','name'=>'Biaya Administrasi','parent_code'=>'500000000'],
            ['code'=>'505000000','name'=>'Biaya Lainnya','parent_code'=>'500000000'],
            ['code'=>'600000000','name'=>'Beban Keuangan','parent_code'=>null],
            ['code'=>'601000000','name'=>'Beban Bunga','parent_code'=>'600000000'],
            ['code'=>'602000000','name'=>'Beban Bank','parent_code'=>'600000000'],
            ['code'=>'700000000','name'=>'Pendapatan Lainnya','parent_code'=>null],
            ['code'=>'701000000','name'=>'Pendapatan Sewa','parent_code'=>'700000000'],
            ['code'=>'702000000','name'=>'Pendapatan Bunga','parent_code'=>'700000000'],
            ['code'=>'800000000','name'=>'Pajak','parent_code'=>null],
            ['code'=>'801000000','name'=>'Pajak Penghasilan Perusahaan','parent_code'=>'800000000'],
            ['code'=>'802000000','name'=>'Pajak Penghasilan Karyawan','parent_code'=>'800000000'],
            ['code'=>'803000000','name'=>'Pajak Pertambahan Nilai (PPN)','parent_code'=>'800000000'],
            ['code'=>'804000000','name'=>'Pajak Lainnya','parent_code'=>'800000000'],
        ];

        DB::transaction(function() use ($rows){
            // First pass: insert or update base rows without parent_id
            foreach($rows as $r){
                Account::updateOrCreate(['code'=>$r['code']], [
                    'name'=>$r['name'],
                    'parent_id'=>null,
                ]);
            }
            // Build lookup of code => id
            $lookup = Account::pluck('id','code');
            // Second pass: update parent_id
            foreach($rows as $r){
                if($r['parent_code']){
                    Account::where('code',$r['code'])->update([
                        'parent_id' => $lookup[$r['parent_code']] ?? null
                    ]);
                }
            }
        });
    }
}
