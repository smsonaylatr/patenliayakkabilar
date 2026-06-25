(function () {
  'use strict';

  if (window.DEDCart && typeof window.DEDCart.add === 'function') {
    return;
  }

  var KEY = 'ded_cart_v1';

  function parseCart() {
    try {
      var raw = localStorage.getItem(KEY);
      var a = raw ? JSON.parse(raw) : [];
      return Array.isArray(a) ? a : [];
    } catch (e) {
      return [];
    }
  }

  function saveCart(lines) {
    localStorage.setItem(KEY, JSON.stringify(lines));
    updateHeaderBadge();
  }

  function fmtTry(n) {
    var x = Number(n);
    if (isNaN(x)) return '';
    var parts = x.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts[0] + '.' + parts[1] + 'TL';
  }

  function lineKey(line) {
    return (line.slug || '') + '\t' + (line.variant || '');
  }

  function normalizeImage(path) {
    if (!path) return '';
    path = String(path).trim();
    if (/^https?:\/\//i.test(path) || path.indexOf('//') === 0) return path;
    while (path.indexOf('../') === 0) path = path.slice(3);
    return path.replace(/^\//, '');
  }

  function resolveImage(line) {
    var map = window.DED_CART_IMAGE_MAP || {};
    if (line && line.slug && map[line.slug]) return map[line.slug];
    return normalizeImage(line && line.image ? line.image : '');
  }

  function updateHeaderBadge() {
    var lines = parseCart();
    var n = lines.reduce(function (s, l) {
      return s + (parseInt(l.qty, 10) || 0);
    }, 0);

    document.querySelectorAll('cart-count').forEach(function (el) {
      el.textContent = String(n);
      if (n > 0) el.classList.remove('opacity-0');
      else el.classList.add('opacity-0');
    });
    document.querySelectorAll('.count-bubble--lg').forEach(function (el) {
      if (el.closest('.header__cart-count')) return;
      el.textContent = String(n);
    });
  }

  window.DEDCart = {
    key: KEY,
    get: parseCart,
    set: saveCart,
    fmtTry: fmtTry,
    normalizeImage: normalizeImage,
    resolveImage: resolveImage,
    syncImagesFromMap: function () {
      var map = window.DED_CART_IMAGE_MAP || {};
      var lines = parseCart();
      var changed = false;
      lines.forEach(function (line) {
        var next = resolveImage(line);
        if (next && line.image !== next) {
          line.image = next;
          changed = true;
        }
      });
      if (changed) saveCart(lines);
    },
    add: function (line) {
      var qty = parseInt(line.qty, 10) || 1;
      var slug = line.slug || '';
      var variant = (line.variant || '').trim();
      var image = resolveImage(line);
      var lines = parseCart();
      var found = -1;
      for (var i = 0; i < lines.length; i++) {
        if (lines[i].slug === slug && (lines[i].variant || '') === variant) {
          found = i;
          break;
        }
      }
      if (found >= 0) {
        lines[found].qty = (parseInt(lines[found].qty, 10) || 0) + qty;
        if (image) lines[found].image = image;
      } else {
        lines.push({
          slug: slug,
          title: line.title || '',
          price: Number(line.price) || 0,
          currency: line.currency || 'TRY',
          image: image,
          variant: variant,
          qty: qty,
        });
      }
      saveCart(lines);
    },
    removeIndex: function (i) {
      var lines = parseCart();
      lines.splice(i, 1);
      saveCart(lines);
    },
    setQty: function (i, q) {
      var lines = parseCart();
      var n = parseInt(q, 10);
      if (isNaN(n) || n < 1) n = 1;
      if (lines[i]) lines[i].qty = n;
      saveCart(lines);
    },
    clear: function () {
      saveCart([]);
    },
    lineKey: lineKey,
  };

  try {
    if (typeof window.dispatchEvent === 'function') {
      window.dispatchEvent(new Event('dedcart-ready'));
    }
  } catch (eReady) {}

  document.addEventListener('DOMContentLoaded', updateHeaderBadge);
})();
