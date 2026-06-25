(function () {
  var cfg = window.YONETIM_ACCOUNTING;
  if (!cfg || typeof ApexCharts === 'undefined') {
    return;
  }

  function sparkOptions(data) {
    return {
      series: [{ data: data }],
      chart: {
        type: 'line',
        width: 120,
        height: 35,
        sparkline: { enabled: true },
        dropShadow: {
          enabled: true,
          top: 4,
          left: 0,
          bottom: 0,
          right: 0,
          blur: 2,
          color: 'rgba(132, 145, 183, 0.3)',
          opacity: 0.35,
        },
      },
      colors: ['#95a0c5'],
      stroke: { show: true, curve: 'smooth', width: [3], lineCap: 'round' },
      tooltip: {
        fixed: { enabled: false },
        x: { show: false },
        y: { title: { formatter: function () { return ''; } } },
        marker: { show: false },
      },
    };
  }

  var el1 = document.querySelector('#line-revenue');
  if (el1) {
    new ApexCharts(el1, sparkOptions(cfg.spark_revenue || [])).render();
  }

  var el2 = document.querySelector('#line-orders');
  if (el2) {
    new ApexCharts(el2, sparkOptions(cfg.spark_orders || [])).render();
  }

  var labels = cfg.chart_labels || [];
  var revenue = cfg.chart_revenue || [];
  var hi = typeof cfg.chart_highlight === 'number' ? cfg.chart_highlight : -1;
  var colors = labels.map(function (_, i) {
    return i === hi ? '#22c55e' : '#95a0c5';
  });

  var maxRev = Math.max.apply(null, revenue.concat([1]));

  var barEl = document.querySelector('#monthly_income');
  if (barEl) {
    new ApexCharts(barEl, {
      chart: {
        height: 270,
        type: 'bar',
        toolbar: { show: false },
        dropShadow: {
          enabled: true,
          top: 0,
          left: 5,
          bottom: 5,
          right: 0,
          blur: 5,
          color: '#45404a2e',
          opacity: 0.35,
        },
      },
      colors: colors,
      plotOptions: {
        bar: {
          borderRadius: 6,
          dataLabels: { position: 'top' },
          columnWidth: '20%',
          distributed: true,
        },
      },
      dataLabels: {
        enabled: true,
        formatter: function (val) {
          if (maxRev <= 0) return '';
          var pct = Math.round((val / maxRev) * 1000) / 10;
          return pct + '%';
        },
        offsetY: -20,
        style: { fontSize: '12px', colors: ['#8997bd'] },
      },
      series: [{ name: 'Ciro', data: revenue }],
      xaxis: {
        categories: labels,
        position: 'top',
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      yaxis: {
        labels: {
          formatter: function (v) {
            if (v >= 1000) return (v / 1000).toFixed(0) + 'k';
            return String(Math.round(v));
          },
        },
      },
      grid: {
        row: { colors: ['transparent', 'transparent'], opacity: 0.2 },
        strokeDashArray: 2.5,
      },
      legend: { show: false },
      tooltip: {
        y: {
          formatter: function (v) {
            return new Intl.NumberFormat('tr-TR', {
              style: 'currency',
              currency: 'TRY',
              maximumFractionDigits: 0,
            }).format(v);
          },
        },
      },
    }).render();
  }

  var donutEl = document.querySelector('#payment_mix');
  if (donutEl) {
    var paid = cfg.payment_paid || 0;
    var awaiting = cfg.payment_awaiting || 0;
    var other = cfg.payment_other || 0;
    if (paid + awaiting + other === 0) {
      paid = 1;
    }
    new ApexCharts(donutEl, {
      chart: { height: 280, type: 'donut' },
      plotOptions: { pie: { donut: { size: '80%' } } },
      dataLabels: { enabled: false },
      stroke: { show: true, width: 2, colors: ['transparent'] },
      series: [paid, awaiting, other],
      labels: ['Ödendi', 'Havale bekliyor', 'Diğer'],
      colors: ['#22c55e', '#08b0e7', '#ffc728'],
      legend: {
        show: true,
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '13px',
        offsetY: 0,
      },
      tooltip: {
        y: {
          formatter: function (v) {
            return v + ' sipariş';
          },
        },
      },
    }).render();
  }
})();
