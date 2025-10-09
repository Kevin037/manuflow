@extends('layouts.admin')
@section('title','Balance Sheet')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Balance Sheet</h1>
        <p class="mt-2 text-sm text-gray-600">Snapshot of assets, liabilities, and equity</p>
      </div>
      <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">As of</label>
          <input type="date" name="as_of" value="{{ $as_of }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">P&L Start</label>
          <input type="date" name="pl_start_date" value="{{ $pl_start_date }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">P&L End</label>
          <input type="date" name="pl_end_date" value="{{ $pl_end_date }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <button class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500 w-full">Generate</button>
        </div>
      </form>
    </div>
    @if($errors->any())
      <div class="mt-4 text-sm text-red-600">{{ $errors->first() }}</div>
    @endif
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Assets -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
      <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Assets</div>
      <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
          <thead class="bg-gray-50/50">
            <tr>
              <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Account</th>
              <th class="px-3 py-3 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach(($report['assets_breakdown'] ?? []) as $row)
              <tr>
                <td class="py-2 pl-6 pr-3 text-sm text-gray-800">{{ $row['account_name'] }}</td>
                <td class="px-3 py-2 text-right text-sm text-gray-900">{{ 'Rp '.number_format($row['amount'] ?? 0,0,',','.') }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="bg-gray-50">
              <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase">Total Assets</th>
              <th class="px-3 py-3 text-right text-xs font-semibold text-gray-900 uppercase">{{ 'Rp '.number_format($report['assets_total'] ?? 0,0,',','.') }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Liabilities -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
      <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Liabilities</div>
      <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
          <thead class="bg-gray-50/50">
            <tr>
              <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Account</th>
              <th class="px-3 py-3 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach(($report['liabilities_breakdown'] ?? []) as $row)
              <tr>
                <td class="py-2 pl-6 pr-3 text-sm text-gray-800">{{ $row['account_name'] }}</td>
                <td class="px-3 py-2 text-right text-sm text-gray-900">{{ 'Rp '.number_format($row['amount'] ?? 0,0,',','.') }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="bg-gray-50">
              <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase">Total Liabilities</th>
              <th class="px-3 py-3 text-right text-xs font-semibold text-gray-900 uppercase">{{ 'Rp '.number_format($report['liabilities_total'] ?? 0,0,',','.') }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Equity -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
      <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Equity</div>
      <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
          <thead class="bg-gray-50/50">
            <tr>
              <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Account</th>
              <th class="px-3 py-3 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach(($report['equities_breakdown'] ?? []) as $row)
              <tr>
                <td class="py-2 pl-6 pr-3 text-sm text-gray-800">{{ $row['account_name'] }}</td>
                <td class="px-3 py-2 text-right text-sm text-gray-900">{{ 'Rp '.number_format($row['amount'] ?? 0,0,',','.') }}</td>
              </tr>
            @endforeach
            <tr>
              <td class="py-2 pl-6 pr-3 text-sm text-gray-800">Net Profit (from P&amp;L)</td>
              <td class="px-3 py-2 text-right text-sm text-gray-900">{{ 'Rp '.number_format($report['net_profit'] ?? 0,0,',','.') }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg-gray-50">
              <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase">Total Equity</th>
              <th class="px-3 py-3 text-right text-xs font-semibold text-gray-900 uppercase">{{ 'Rp '.number_format($report['equities_total'] ?? 0,0,',','.') }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <!-- Summary -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-center">
      <div>
        <div class="text-sm text-gray-500">Assets</div>
        <div class="text-lg font-semibold">{{ 'Rp '.number_format($report['assets_total'] ?? 0,0,',','.') }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Liabilities</div>
        <div class="text-lg font-semibold">{{ 'Rp '.number_format($report['liabilities_total'] ?? 0,0,',','.') }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Equity (incl. Net Profit)</div>
        <div class="text-lg font-semibold">{{ 'Rp '.number_format($report['equities_total'] ?? 0,0,',','.') }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Balanced</div>
        @php $ok = $report['balanced'] ?? false; @endphp
        <div class="text-lg font-semibold {{ $ok ? 'text-emerald-700' : 'text-rose-700' }}">{{ $ok ? 'Yes' : 'No' }}</div>
      </div>
    </div>
  </div>
</div>
@endsection