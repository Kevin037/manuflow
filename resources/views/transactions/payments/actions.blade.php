@php /** @var \App\Models\Payment $payment */ @endphp
<div class="flex items-center gap-x-2">
  <a href="{{ route('payments.show',$payment) }}" class="inline-flex items-center justify-center rounded-md bg-gray-100 p-2 text-gray-600 hover:bg-gray-200" title="View">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
  </a>
  <a href="{{ route('payments.edit',$payment) }}" class="inline-flex items-center justify-center rounded-md bg-amber-100 p-2 text-amber-700 hover:bg-amber-200" title="Edit">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
  </a>
  <a href="{{ route('payments.export',$payment) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-md bg-indigo-100 p-2 text-indigo-700 hover:bg-indigo-200" title="Export PDF">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8m8-10H8a2 2 0 00-2 2v12a2 2 0 002 2h4l4 4V4a2 2 0 00-2-2z"/></svg>
  </a>
  <button type="button" data-url="{{ route('payments.destroy',$payment) }}" class="delete-btn inline-flex items-center justify-center rounded-md bg-red-100 p-2 text-red-600 hover:bg-red-200" title="Delete">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 4h.01"/></svg>
  </button>
</div>