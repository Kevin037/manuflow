<div id="monthly-summary" class="mb-8">
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <!-- Revenue Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100">
            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3m0 0c0 1.657 1.343 3 3 3m-3-3h6m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="ml-4 flex-1">
          <dt class="text-sm font-medium text-gray-500">Revenue (This Month)</dt>
          <dd class="flex items-baseline">
            <div id="rev-current" class="text-2xl font-semibold text-gray-900">—</div>
            <div class="ml-2 flex items-baseline text-sm">
              <span id="rev-change" class="text-gray-500">—</span>
              <span class="ml-1 text-gray-500">vs last month</span>
            </div>
          </dd>
        </div>
      </div>
    </div>

  <!-- Purchase Orders Total Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
            </svg>
          </div>
        </div>
        <div class="ml-4 flex-1">
          <dt class="text-sm font-medium text-gray-500">Purchase Orders (Total)</dt>
          <dd class="flex items-baseline">
            <div id="po-current" class="text-2xl font-semibold text-gray-900">—</div>
            <div class="ml-2 flex items-baseline text-sm">
              <span id="po-change" class="text-gray-500">—</span>
              <span class="ml-1 text-gray-500">vs last month</span>
            </div>
          </dd>
        </div>
      </div>
    </div>

    <!-- Net Profit Card -->
    <div class="relative overflow-hidden rounded-xl bg-white px-4 py-5 shadow-md sm:p-6 border border-gray-100">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l-3-3m3 3l3-3m6 3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="ml-4 flex-1">
          <dt class="text-sm font-medium text-gray-500">Net Profit (This Month)</dt>
          <dd class="flex items-baseline">
            <div id="np-current" class="text-2xl font-semibold text-gray-900">—</div>
            <div class="ml-2 flex items-baseline text-sm">
              <span id="np-change" class="text-gray-500">—</span>
              <span class="ml-1 text-gray-500">vs last month</span>
            </div>
          </dd>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading & Error States -->
  <div id="ms-loading" class="mt-3 text-sm text-gray-500">Loading monthly summary…</div>
  <div id="ms-error" class="mt-3 text-sm text-red-600 hidden">Failed to load monthly summary.</div>
</div>

@push('scripts')
<script>
(function(){
  const formatIDR = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
  const formatInt = (n) => new Intl.NumberFormat('id-ID').format(n);

  function setChange(el, percent) {
    if (percent === null || percent === undefined) {
      el.textContent = '—';
      el.classList.remove('text-green-600','text-red-600');
      el.classList.add('text-gray-500');
      return;
    }
    const arrow = percent > 0 ? '▲' : (percent < 0 ? '▼' : '▶');
    const cls = percent > 0 ? 'text-green-600' : (percent < 0 ? 'text-red-600' : 'text-gray-500');
    el.textContent = `${arrow} ${Math.abs(percent).toFixed(2)}%`;
    el.classList.remove('text-green-600','text-red-600','text-gray-500');
    el.classList.add(cls);
  }

  async function loadMonthlySummary(){
    const loadingEl = document.getElementById('ms-loading');
    const errEl = document.getElementById('ms-error');
    try {
      errEl.classList.add('hidden');
      loadingEl.classList.remove('hidden');
      const res = await fetch("{{ route('reports.monthly_summary') }}", { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('Network response was not ok');
      const data = await res.json();

      const m = data.metrics || {};

      // Revenue
      document.getElementById('rev-current').textContent = formatIDR.format(m.total_revenue?.current || 0);
      setChange(document.getElementById('rev-change'), m.total_revenue?.percent_change ?? null);

  // Purchase Orders Total (currency)
  document.getElementById('po-current').textContent = formatIDR.format(m.total_purchase_orders?.current || 0);
  setChange(document.getElementById('po-change'), m.total_purchase_orders?.percent_change ?? null);

      // Net Profit
      document.getElementById('np-current').textContent = formatIDR.format(m.total_net_profit?.current || 0);
      setChange(document.getElementById('np-change'), m.total_net_profit?.percent_change ?? null);

    } catch (e) {
      console.error(e);
      errEl.classList.remove('hidden');
    } finally {
      loadingEl.classList.add('hidden');
    }
  }

  document.addEventListener('DOMContentLoaded', loadMonthlySummary);
})();
</script>
@endpush
