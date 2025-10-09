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