(function () {
  'use strict';

  function parsePrice(raw) {
    var s = String(raw == null ? '' : raw).trim();
    if (!s) return 0;
    s = s.replace(/[^\d.,\-]/g, '');
    if (!s || s === '-') return 0;

    var lastComma = s.lastIndexOf(',');
    var lastDot = s.lastIndexOf('.');

    if (lastComma >= 0 && lastDot >= 0) {
      if (lastComma > lastDot) {
        s = s.replace(/\./g, '').replace(',', '.');
      } else {
        s = s.replace(/,/g, '');
      }
    } else if (lastComma >= 0) {
      var decLen = s.length - lastComma - 1;
      if (decLen > 0 && decLen <= 2) {
        s = s.replace(/\./g, '').replace(',', '.');
      } else {
        s = s.replace(/,/g, '');
      }
    } else if (lastDot >= 0) {
      var decLen2 = s.length - lastDot - 1;
      var dots = (s.match(/\./g) || []).length;
      if (dots > 1 || decLen2 > 2) {
        s = s.replace(/\./g, '');
      }
    }

    var n = parseFloat(s);
    return isNaN(n) || n < 0 ? 0 : n;
  }

  function formatTr(n) {
    var x = Number(n);
    if (isNaN(x)) return '';
    return x.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatStorefront(n) {
    var x = Number(n);
    if (isNaN(x)) return '';
    var parts = x.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts[0] + '.' + parts[1] + 'TL';
  }

  function bindInput(inp) {
    var preview =
      inp.parentNode && inp.parentNode.querySelector('[data-yun-price-preview]')
        ? inp.parentNode.querySelector('[data-yun-price-preview]')
        : null;

    function refreshPreview() {
      if (!preview) return;
      var n = parsePrice(inp.value);
      preview.textContent =
        n > 0 ? 'Vitrinde görünecek: ' + formatStorefront(n) + ' (≈ ' + formatTr(n) + ' TL)' : '';
    }

    inp.addEventListener('input', refreshPreview);
    inp.addEventListener('blur', function () {
      var n = parsePrice(inp.value);
      if (n > 0) {
        inp.value = formatTr(n);
      }
      refreshPreview();
    });
    refreshPreview();
  }

  window.yunBindPriceInput = bindInput;
  document.querySelectorAll('[data-yun-price]').forEach(bindInput);
})();
