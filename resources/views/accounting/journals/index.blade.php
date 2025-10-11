@extends('layouts.admin')
@section('title','Journal Entries')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Journal Entries</h1>
        <p class="mt-2 text-sm text-gray-600">Grouped by transaction</p>
      </div>
    </div>
  </div>

  <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
    <!-- Filters -->
    <div class="px-6 py-4 border-b border-gray-200">
      <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
        <div class="flex-1 min-w-0">
          <input type="date" name="dt_start" value="{{ $dt_start }}" 
                 class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div class="flex-1 min-w-0">
          <input type="date" name="dt_end" value="{{ $dt_end }}" 
                 class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div class="flex gap-x-2 flex-shrink-0">
          <button type="submit" data-no-spinner
                  class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
            </svg>
            Filter
          </button>
          <a href="{{ route('journals.export.excel') }}?dt_start={{ $dt_start }}&dt_end={{ $dt_end }}" 
             class="inline-flex items-center gap-x-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-all duration-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
          </a>
        </div>
      </form>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tanggal</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Kode Akun</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Nama Akun</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Debit</th>
            <th class="px-3 py-4 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Kredit</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($groups as $key => $lines)
            @foreach($lines as $i => $e)
              <tr class="hover:bg-gray-50 transition">
                <td class="py-4 pl-6 pr-3 text-sm text-gray-900">{{ $e->dt->format('d M Y') }}</td>
                <td class="px-3 py-4 text-sm text-gray-900">{{ $e->account?->code }}</td>
                <td class="px-3 py-4 text-sm text-gray-900">{{ $e->account?->name }}</td>
                <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $e->debit ? 'Rp '.number_format($e->debit,0,',','.') : '-' }}</td>
                <td class="px-3 py-4 text-right text-sm text-gray-900">{{ $e->credit ? 'Rp '.number_format($e->credit,0,',','.') : '-' }}</td>
              </tr>
            @endforeach
            <tr>
              <td colspan="5" class="py-2 text-xs text-gray-400 italic pl-6">Ref: {{ $key }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Tidak ada jurnal untuk periode ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection