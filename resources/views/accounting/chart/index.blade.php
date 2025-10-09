@extends('layouts.admin')
@section('title','Chart of Account')
@section('content')
<div class="space-y-8">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Chart of Account</h1>
        <p class="mt-2 text-sm text-gray-600">Daftar akun beserta parent</p>
      </div>
    </div>
  </div>

  <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Kode Akun</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Nama Akun</th>
            <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Parent</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($accounts as $acc)
            <tr class="hover:bg-gray-50 transition">
              <td class="py-4 pl-6 pr-3 text-sm text-gray-900">{{ $acc->code }}</td>
              <td class="px-3 py-4 text-sm text-gray-900">{{ $acc->name }}</td>
              <td class="px-3 py-4 text-sm text-gray-900">{{ $acc->parent?->name ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada akun.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection