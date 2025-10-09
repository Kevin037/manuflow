@extends('layouts.admin')
@section('title','Trial Balance')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Trial Balance</h1>
        <p class="mt-2 text-sm text-gray-600">Daftar akun (child only) dengan periode yang dipilih</p>
      </div>
      <form method="GET" class="flex items-end gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
          <input type="date" name="dt_start" value="{{ $dt_start }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
          <input type="date" name="dt_end" value="{{ $dt_end }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500">Filter</button>
      </form>
    </div>
  </div>

  <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">ID</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Kode akun</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Nama Akun</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Saldo Awal Debit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Saldo Awal Kredit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Mutasi Debit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Mutasi Kredit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Saldo Akhir Debit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Saldo Akhir Kredit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Saldo Akhir (Debit-kredit)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($rows as $r)
            <tr class="hover:bg-gray-50 transition">
              <td class="py-4 pl-6 pr-3 text-sm text-gray-900">{{ $r['id'] }}</td>
              <td class="px-3 py-4 text-sm text-gray-900">{{ $r['code'] }}</td>
              <td class="px-3 py-4 text-sm text-gray-900">{{ $r['name'] }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $r['saldoAwalDebit'] ? 'Rp '.number_format($r['saldoAwalDebit'],0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $r['saldoAwalKredit'] ? 'Rp '.number_format($r['saldoAwalKredit'],0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $r['mutasiDebit'] ? 'Rp '.number_format($r['mutasiDebit'],0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $r['mutasiKredit'] ? 'Rp '.number_format($r['mutasiKredit'],0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $r['saldoAkhirDebit'] ? 'Rp '.number_format($r['saldoAkhirDebit'],0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $r['saldoAkhirKredit'] ? 'Rp '.number_format($r['saldoAkhirKredit'],0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm font-semibold {{ $r['saldoAkhir'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $r['saldoAkhir'] ? 'Rp '.number_format($r['saldoAkhir'],0,',','.') : '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="px-6 py-10 text-center text-sm text-gray-500">Tidak ada data untuk periode ini.</td>
            </tr>
          @endforelse
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider" colspan="3">TOTAL</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $totals['saldoAwalDebit'] ? 'Rp '.number_format($totals['saldoAwalDebit'],0,',','.') : '-' }}</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $totals['saldoAwalKredit'] ? 'Rp '.number_format($totals['saldoAwalKredit'],0,',','.') : '-' }}</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $totals['mutasiDebit'] ? 'Rp '.number_format($totals['mutasiDebit'],0,',','.') : '-' }}</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $totals['mutasiKredit'] ? 'Rp '.number_format($totals['mutasiKredit'],0,',','.') : '-' }}</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $totals['saldoAkhirDebit'] ? 'Rp '.number_format($totals['saldoAkhirDebit'],0,',','.') : '-' }}</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $totals['saldoAkhirKredit'] ? 'Rp '.number_format($totals['saldoAkhirKredit'],0,',','.') : '-' }}</th>
            <th class="px-3 py-4 text-right text-xs font-semibold {{ $totals['saldoAkhir'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $totals['saldoAkhir'] ? 'Rp '.number_format($totals['saldoAkhir'],0,',','.') : '-' }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
