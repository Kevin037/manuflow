@extends('layouts.admin')
@section('title','Profit & Loss')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Profit &amp; Loss</h1>
        <p class="mt-2 text-sm text-gray-600">Pilih periode untuk menghitung laba rugi</p>
      </div>
      <form method="GET" class="flex items-end gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
          <input type="date" name="start_date" value="{{ $start_date }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
          <input type="date" name="end_date" value="{{ $end_date }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500">Generate</button>
      </form>
    </div>
    @if($errors->any())
      <div class="mt-4 text-sm text-red-600">{{ $errors->first() }}</div>
    @endif
  </div>

  <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Deskripsi</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Jumlah</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr>
            <td class="py-4 pl-6 pr-3 text-sm text-gray-900">Total Sales</td>
            <td class="px-3 py-4 text-right text-sm text-gray-900">{{ 'Rp '.number_format($results['total_sales'] ?? 0,0,',','.') }}</td>
          </tr>
          <tr>
            <td class="py-4 pl-6 pr-3 text-sm text-gray-900">Total HPP</td>
            <td class="px-3 py-4 text-right text-sm text-gray-900">{{ 'Rp '.number_format($results['total_hpp'] ?? 0,0,',','.') }}</td>
          </tr>
          <tr>
            <td class="py-4 pl-6 pr-3 text-sm text-gray-900">Total Expenses</td>
            <td class="px-3 py-4 text-right text-sm text-gray-900">{{ 'Rp '.number_format($results['total_expenses'] ?? 0,0,',','.') }}</td>
          </tr>
          <tr class="bg-gray-50">
            <td class="py-4 pl-6 pr-3 text-sm font-semibold text-gray-900">Gross Profit</td>
            <td class="px-3 py-4 text-right text-sm font-semibold text-gray-900">{{ 'Rp '.number_format($results['gross_profit'] ?? 0,0,',','.') }}</td>
          </tr>
          <tr class="bg-gray-50">
            <td class="py-4 pl-6 pr-3 text-sm font-semibold text-gray-900">Net Profit</td>
            <td class="px-3 py-4 text-right text-sm font-semibold {{ ($results['net_profit'] ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ 'Rp '.number_format($results['net_profit'] ?? 0,0,',','.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Detailed P&L by Account -->
  <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Keterangan</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Jumlah</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @php
            $groups = $detailed['groups'] ?? [];
            $summary = $detailed['summary'] ?? [];
          @endphp

          @foreach (['4','7','5','6','8'] as $gkey)
            @if(isset($groups[$gkey]) && (!empty($groups[$gkey]['accounts']) || abs($groups[$gkey]['total']) > 0))
              <tr>
                <td class="py-3 pl-6 pr-3 text-sm font-extrabold text-gray-900">{{ strtoupper($groups[$gkey]['label']) }}</td>
                <td class="px-3 py-3 text-right text-sm font-extrabold text-gray-900">{{ 'Rp '.number_format($groups[$gkey]['total'],0,',','.') }}</td>
              </tr>
              @foreach($groups[$gkey]['accounts'] as $acc)
                <tr>
                  <td class="py-2 pl-10 pr-3 text-sm text-gray-800">{{ $acc['name'] }}</td>
                  <td class="px-3 py-2 text-right text-sm text-gray-900">{{ 'Rp '.number_format($acc['amount'],0,',','.') }}</td>
                </tr>
              @endforeach
              <tr>
                <td class="py-2 pl-6 pr-3 text-sm font-bold text-gray-900">TOTAL {{ strtoupper($groups[$gkey]['label']) }}</td>
                <td class="px-3 py-2 text-right text-sm font-bold text-gray-900">{{ 'Rp '.number_format($groups[$gkey]['total'],0,',','.') }}</td>
              </tr>
              <tr><td colspan="2" class="py-1"></td></tr>
            @endif
          @endforeach

          <!-- Summary rows akin to the reference total sections -->
          <tr class="bg-gray-50">
            <td class="py-3 pl-6 pr-3 text-sm font-semibold text-gray-900">Gross Profit (Pendapatan - Biaya/HPP)</td>
            <td class="px-3 py-3 text-right text-sm font-semibold text-gray-900">{{ 'Rp '.number_format($summary['gross_profit'] ?? 0,0,',','.') }}</td>
          </tr>
          <tr class="bg-gray-50">
            <td class="py-3 pl-6 pr-3 text-sm font-semibold text-gray-900">Net Profit</td>
            <td class="px-3 py-3 text-right text-sm font-semibold {{ ($summary['net_profit'] ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ 'Rp '.number_format($summary['net_profit'] ?? 0,0,',','.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
