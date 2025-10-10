<div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 sm:p-6">
  <div class="flex items-center justify-between mb-3">
    <div>
      <h3 class="text-base font-semibold text-gray-900">Top Products — Monthly Revenue</h3>
      <p class="text-xs text-gray-500" id="mtp-rangeText">Latest 12 months</p>
    </div>
    <div class="flex items-center space-x-2">
      <div class="inline-flex rounded-md shadow-sm" role="group">
        <button id="btnBar" type="button" class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-l-md bg-gray-100 text-gray-700 hover:bg-gray-200">Monthly Bar</button>
        <button id="btnPie" type="button" class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-r-md bg-white text-gray-700 hover:bg-gray-50">Pie snapshot</button>
      </div>
      <select id="mtp-months" class="rounded-md border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        <option value="6">Last 6 months</option>
        <option value="12" selected>Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </div>
  </div>
  <div class="relative">
    <canvas id="mtp-chart" height="120"></canvas>
  </div>
</div>

<!-- Modal for drilldown (Tailwind) -->
<div id="monthlyTopProductsModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
  <div id="mtp-overlay" class="fixed inset-0 bg-black/50"></div>
  <div class="flex min-h-full items-center justify-center p-4">
    <div class="w-full max-w-5xl rounded-xl bg-white shadow-xl">
      <div class="flex items-center justify-between border-b px-4 py-3">
        <h5 class="text-lg font-semibold" id="mtp-modal-title">Top 5 Products</h5>
        <button id="mtp-close" class="inline-flex h-8 w-8 items-center justify-center rounded hover:bg-gray-100" aria-label="Close">
          <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
      </div>
      <div class="px-4 py-3">
        <div class="flex items-center justify-between mb-3">
          <div id="mtp-modal-subtitle" class="text-sm text-gray-600"></div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Top K</span>
            <span class="inline-flex items-center rounded border border-gray-200 px-2 py-1 text-xs text-gray-700 bg-gray-50 select-none">5</span>
          </div>
        </div>
        <div id="mtp-loading" class="py-8 text-center hidden">
          <svg class="mx-auto h-6 w-6 animate-spin text-primary-600" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        </div>
        <div id="mtp-empty" class="hidden rounded border border-blue-100 bg-blue-50 px-3 py-2 text-sm text-blue-700">No revenue for this month.</div>
        <div id="mtp-table-wrap" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="w-16 px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rank</th>
                <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
                <th class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Revenue</th>
                <th class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">% of Month</th>
              </tr>
            </thead>
            <tbody id="mtp-tbody" class="divide-y divide-gray-100 bg-white"></tbody>
          </table>
        </div>
        <div id="mtp-pie-wrap" class="mt-6 hidden">
          <canvas id="mtp-pie" height="120"></canvas>
        </div>
      </div>
      <div class="flex items-center justify-between border-t px-4 py-3">
        <button id="mtp-export" type="button" class="inline-flex items-center rounded border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Export CSV</button>
        <button id="mtp-close-footer" type="button" class="inline-flex items-center rounded bg-primary-600 px-3 py-2 text-sm font-medium text-white hover:bg-primary-500">Close</button>
      </div>
    </div>
  </div>
  </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  const canvas = document.getElementById('mtp-chart');
  if (!canvas) return;

  const monthsSel = document.getElementById('mtp-months');
  const rangeText = document.getElementById('mtp-rangeText');
  const btnBar = document.getElementById('btnBar');
  const btnPie = document.getElementById('btnPie');

  let chartBar;
  let chartPie;
  let latestSeries; // cache last fetched monthly series

  function fmtIDR(v){
    try { return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:2}).format(v); } catch(_) {
      return new Intl.NumberFormat(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}).format(v);
    }
  }
  const fmtPct = (v) => new Intl.NumberFormat('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}).format(v) + '%';
  const percentOf = (part, total) => (!total || total <= 0) ? 0 : (part / total) * 100;

  async function fetchJSON(url){
    const res = await fetch(url, {headers:{'Accept':'application/json'}});
    if (!res.ok) throw new Error('Request failed');
    return res.json();
  }

  async function loadSeries(months){
    const url = new URL('{{ route('reports.monthly_top_products') }}', window.location.origin);
    if (months) url.searchParams.set('months', months);
    const data = await fetchJSON(url);
    latestSeries = data;
    rangeText.textContent = `${data.meta.start_date} → ${data.meta.end_date}`;
    return data;
  }

  // Tailwind modal controls (no global click handlers)
  const modalEl = document.getElementById('monthlyTopProductsModal');
  const overlayEl = document.getElementById('mtp-overlay');
  const closeBtn = document.getElementById('mtp-close');
  const closeBtnFooter = document.getElementById('mtp-close-footer');
  function showModal(){ modalEl.classList.remove('hidden'); }
  function hideModal(){ modalEl.classList.add('hidden'); }
  overlayEl.addEventListener('click', hideModal);
  closeBtn.addEventListener('click', hideModal);
  closeBtnFooter.addEventListener('click', hideModal);

  function renderBar(data){
    const ctx = canvas.getContext('2d');
    latestSeries = data; // ensure months available for clicks
    const cfg = {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Total Revenue',
          data: data.revenues,
          backgroundColor: 'rgba(99, 102, 241, 0.6)', // indigo-500
          borderColor: '#6366f1',
          borderWidth: 1,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 400 },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: 'rgba(0,0,0,0.05)' }, beginAtZero: true }
        },
        plugins: {
          tooltip: {
            callbacks: { label: (ctx) => `${ctx.dataset.label}: ${fmtIDR(ctx.parsed.y)}` }
          },
          legend: { display: true }
        }
      }
    };

    if (chartBar) { chartBar.destroy(); }
    chartBar = new Chart(ctx, cfg);
    // Robust click handler using nearest mode
    canvas.onclick = async (evt) => {
      const points = chartBar.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
      if (!points || !points.length) return;
      const idx = points[0].index;
      const monthsArr = (latestSeries && latestSeries.months) ? latestSeries.months : [];
      if (!monthsArr[idx]) { console.warn('months metadata missing at index', idx); return; }
      const y = parseInt(monthsArr[idx].year, 10);
      const m = parseInt(monthsArr[idx].month, 10);
      await openDrilldown(y, m);
    };
  }

  // Remove global click handlers to avoid random close; rely on Bootstrap's default behavior

  async function openDrilldown(year, month){
    const tbody = document.getElementById('mtp-tbody');
    const loading = document.getElementById('mtp-loading');
    const title = document.getElementById('mtp-modal-title');
    const subtitle = document.getElementById('mtp-modal-subtitle');

    tbody.innerHTML = '';
  document.getElementById('mtp-empty').classList.add('hidden');
  document.getElementById('mtp-table-wrap').classList.add('hidden');
  document.getElementById('mtp-pie-wrap').classList.add('hidden');
  loading.classList.remove('hidden');
    // Build drilldown URL using named route with placeholders to ensure correct base path
    const drillBase = @json(route('reports.monthly_top_products.drilldown', ['year' => '__YEAR__', 'month' => '__MONTH__']));
    const urlStr = drillBase.replace('__YEAR__', String(year)).replace('__MONTH__', String(month));
    try {
      // Basic sanity check before request
      if (!Number.isInteger(year) || !Number.isInteger(month) || month < 1 || month > 12) {
        throw new Error('Invalid year/month parsed');
      }
      const data = await fetchJSON(urlStr);
      title.textContent = `Top 5 Products — ${data.label} (Total Revenue: ${fmtIDR(data.total_month_revenue)})`;
      subtitle.textContent = '';

      const rows = Array.isArray(data.top_products) ? data.top_products : [];
      const total = Number(data.total_month_revenue || 0);
      if (total <= 0) {
        document.getElementById('mtp-empty').classList.remove('hidden');
      } else {
        const html = rows.map(r => {
          const rev = Number(r.revenue || 0);
          const pct = percentOf(rev, total);
          return `<tr><td class=\"px-3 py-2\">${r.rank ?? ''}</td><td class=\"px-3 py-2\">${r.product_name || '-'}</td><td class=\"px-3 py-2 text-right\">${fmtIDR(rev)}</td><td class=\"px-3 py-2 text-right\">${fmtPct(pct)}</td></tr>`;
        }).join('');
        tbody.innerHTML = html;
        document.getElementById('mtp-table-wrap').classList.remove('hidden');
      }

      // Pie snapshot for this month (top 5)
      const pieWrap = document.getElementById('mtp-pie-wrap');
      const pieCanvas = document.getElementById('mtp-pie');
      const colors = ['#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa'];
      if (chartPie) chartPie.destroy();
      chartPie = new Chart(pieCanvas.getContext('2d'), {
        type: 'pie',
        data: {
          labels: rows.map(r=>r.product_name),
          datasets: [{ data: rows.map(r=>Number(r.revenue||0)), backgroundColor: colors }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });
  if (total > 0) pieWrap.classList.remove('hidden');

      // CSV export button
      document.getElementById('mtp-export').onclick = () => {
        const header = ['Rank','Product','Revenue','Percent'];
        const csvRows = [header.join(',')];
        for (const r of rows) {
          const rev = Number(r.revenue||0);
          const pct = percentOf(rev, total);
          csvRows.push([r.rank, '"'+(r.product_name||'')+'"', rev.toFixed(2), pct.toFixed(2)].join(','));
        }
        const blob = new Blob([csvRows.join('\n')], {type: 'text/csv'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `top5-products-${year}-${String(month).padStart(2,'0')}.csv`;
        a.click();
      };

    } catch (e) {
      console.error('Drilldown error:', e);
      alert('Failed to load drilldown');
      return; // keep modal closed on error
    } finally {
      loading.classList.add('hidden');
      showModal();
    }
  }

  async function init(){
    const data = await loadSeries(12);
    renderBar(data);
  }

  monthsSel.addEventListener('change', async ()=>{
    const m = parseInt(monthsSel.value,10) || 12;
    const data = await loadSeries(m);
    renderBar(data);
  });

  // Toggle buttons (bar default, pie shows latest month top 5)
  btnBar.addEventListener('click', async ()=>{
    btnBar.classList.add('bg-gray-100');
    btnPie.classList.remove('bg-gray-100');
    const m = parseInt(monthsSel.value,10) || 12;
    const data = latestSeries || await loadSeries(m);
    renderBar(data);
  });

  btnPie.addEventListener('click', async ()=>{
    btnPie.classList.add('bg-gray-100');
    btnBar.classList.remove('bg-gray-100');
    const data = latestSeries || await loadSeries(parseInt(monthsSel.value,10)||12);
    const monthsArr = data.months || [];
    if (!monthsArr.length) { console.warn('months metadata missing'); return; }
    const last = monthsArr[monthsArr.length - 1];
    await openDrilldown(parseInt(last.year,10), parseInt(last.month,10));
  });

  init().catch(err => console.error(err));
})();
</script>
@endpush