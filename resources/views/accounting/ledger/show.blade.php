@extends('layouts.admin')
@section('title','Buku Besar - '.$account->name)
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div>
        <a href="{{ route('ledger.index') }}" class="text-gray-400 hover:text-gray-600 mr-6 inline-flex items-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Buku Besar: {{ $account->code }} - {{ $account->name }}</h1>
      </div>
      <form method="GET" class="flex items-end gap-4">
        <input type="hidden" name="dt_start" value="{{ $dt_start }}">
        <input type="hidden" name="dt_end" value="{{ $dt_end }}">
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
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tanggal</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">No Dokumen</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Penjelasan</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Nama Akun</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Debit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Kredit</th>
            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Saldo</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @php $running = 0; @endphp
          @forelse($entries as $e)
            @php $running += ($e->debit - $e->credit); @endphp
            <tr class="hover:bg-gray-50 transition">
              <td class="py-4 pl-6 pr-3 text-sm text-gray-900">{{ $e->dt->format('d M Y') }}</td>
              <td class="px-3 py-4 text-sm text-gray-900">
                @php
                  $docNo = null;
                  if($e->transaction){
                    $docNo = $e->transaction->no ?? ($e->transaction->id ?? null);
                  }
                @endphp
                {{ $docNo ?? '-' }}
              </td>
              <td class="px-3 py-4 text-sm text-gray-900">{{ $e->desc ?? '-' }}</td>
              <td class="px-3 py-4 text-sm text-gray-900">{{ $e->account->name }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $e->debit ? 'Rp '.number_format($e->debit,0,',','.') : '-' }}</td>
              <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $e->credit ? 'Rp '.number_format($e->credit,0,',','.') : '-' }}</td>
              <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ 'Rp '.number_format($running,0,',','.') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">Tidak ada transaksi untuk periode ini.</td></tr>
          @endforelse
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="4" class="py-4 pl-6 pr-3 text-right text-sm font-semibold text-gray-900">Total</td>
            <td class="px-3 py-4 text-right text-sm font-semibold text-gray-900">{{ 'Rp '.number_format($totalDebit,0,',','.') }}</td>
            <td class="px-3 py-4 text-right text-sm font-semibold text-gray-900">{{ 'Rp '.number_format($totalCredit,0,',','.') }}</td>
            <td class="px-6 py-4 text-right text-sm font-bold text-primary-700">{{ 'Rp '.number_format($totalBalance,0,',','.') }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection