<div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 sm:p-6">
  <div class="flex items-center justify-between mb-3">
    <div>
      <h3 class="text-base font-semibold text-gray-900">12-month Sales & Profit Growth</h3>
      <p class="text-xs text-gray-500" id="mg-rangeText">Latest 12 months</p>
    </div>
    <div class="flex items-center space-x-2">
      <select id="mg-range" class="rounded-md border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        <option value="6">Last 6 months</option>
        <option value="12" selected>Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </div>
  </div>
  <div class="relative">
    <canvas id="mg-chart" height="120"></canvas>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  const elCanvas = document.getElementById('mg-chart');
  if (!elCanvas) return;

  const rangeSel = document.getElementById('mg-range');
  const rangeText = document.getElementById('mg-rangeText');

  let chart;

  function currencyFmt(v){
    try {
      return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'IDR', minimumFractionDigits: 2 }).format(v);
    } catch (e) {
      // Fallback without currency if locale/currency not available
      return new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v);
    }
  }

  function buildGradient(ctx, area){
    const gradient = ctx.createLinearGradient(0, 0, 0, area.bottom);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.00)');
    return gradient;
  }

  async function loadData(months){
    const url = new URL('{{ route('reports.monthly_growth') }}', window.location.origin);
    if (months) url.searchParams.set('months', months);
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Failed to load growth data');
    return res.json();
  }

  async function render(months){
    const data = await loadData(months);

    rangeText.textContent = `${data.meta.start_date} → ${data.meta.end_date}`;

    const ctx = elCanvas.getContext('2d');

    const salesColor = '#3b82f6'; // blue-500
    const profitColor = '#10b981'; // emerald-500

    // Negative profit points in red
    const profitPoints = data.profit.map(v => v < 0 ? '#ef4444' : profitColor);

    const cfg = {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'Sales',
            data: data.sales,
            borderColor: salesColor,
            backgroundColor: (ctxArg) => {
              const {chart} = ctxArg;
              const {ctx: c, chartArea} = chart;
              if (!chartArea) return salesColor;
              return buildGradient(c, chartArea);
            },
            fill: true,
            tension: 0.35,
            pointRadius: 3,
            pointHoverRadius: 5,
            borderWidth: 2,
          },
          {
            label: 'Net Profit',
            data: data.profit,
            borderColor: profitColor,
            pointBackgroundColor: profitPoints,
            backgroundColor: 'transparent',
            fill: false,
            tension: 0.35,
            pointRadius: 3,
            pointHoverRadius: 5,
            borderWidth: 2,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: { display: true, color: 'rgba(0,0,0,0.05)' },
          },
          y: {
            grid: { display: true, color: 'rgba(0,0,0,0.05)' },
            ticks: {
              callback: (v) => new Intl.NumberFormat(undefined, {maximumFractionDigits: 2}).format(v)
            }
          }
        },
        plugins: {
          legend: { display: true },
          tooltip: {
            callbacks: {
              label: function(ctx){
                const label = ctx.dataset.label || '';
                const val = ctx.parsed.y;
                return `${label}: ${currencyFmt(val)}`;
              }
            }
          }
        }
      }
    };

    // Manage chart instance lifecycle for smooth update
    if (chart) {
      chart.data.labels = cfg.data.labels;
      chart.data.datasets[0].data = cfg.data.datasets[0].data;
      chart.data.datasets[0].backgroundColor = cfg.data.datasets[0].backgroundColor;
      chart.data.datasets[1].data = cfg.data.datasets[1].data;
      chart.data.datasets[1].pointBackgroundColor = cfg.data.datasets[1].pointBackgroundColor;
      chart.update();
    } else {
      chart = new Chart(ctx, cfg);
    }
  }

  // Initialize
  render(12).catch(err => console.error(err));

  // Range change handler
  rangeSel.addEventListener('change', () => {
    const m = parseInt(rangeSel.value, 10) || 12;
    render(m).catch(err => console.error(err));
  });
})();
</script>
@endpush
