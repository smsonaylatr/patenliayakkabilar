(function () {
  'use strict';

  if (window.__dedSepetUrunInit) return;
  window.__dedSepetUrunInit = true;

  function dedEnsureSepetCekirdek() {
    if (window.DEDCart && typeof window.DEDCart.add === 'function') return;

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
        var i;
        for (i = 0; i < lines.length; i++) {
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
  }

  dedEnsureSepetCekirdek();

  function formatTry(n) {
    var x = Number(n);
    if (isNaN(x)) return '';
    var parts = x.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts[0] + '.' + parts[1] + 'TL';
  }

  function parseMoneyNearForm(form) {
    if (!form) return 0;
    var scope =
      form.closest('quick-buy-drawer') ||
      form.closest('.product-info') ||
      form.closest('product-quick-add') ||
      document;
    var sale = scope.querySelector('sale-price');
    if (!sale) sale = document.querySelector('sale-price');
    if (!sale) return 0;
    var t = (sale.textContent || '').replace(/\u00a0/g, ' ');
    t = t.replace(/[^\d.,]/g, '');
    if (!t) return 0;

    var lastComma = t.lastIndexOf(',');
    var lastDot = t.lastIndexOf('.');

    if (lastComma !== -1 && lastDot !== -1) {
      if (lastComma > lastDot) {
        t = t.replace(/\./g, '').replace(',', '.');
      } else {
        t = t.replace(/,/g, '');
      }
    } else if (lastComma !== -1) {
      var decLen = t.length - lastComma - 1;
      if (decLen > 0 && decLen <= 2) {
        t = t.replace(/\./g, '').replace(',', '.');
      } else {
        t = t.replace(/,/g, '');
      }
    } else if (lastDot !== -1) {
      var dots = (t.match(/\./g) || []).length;
      var decLenD = t.length - lastDot - 1;
      if (dots > 1 || decLenD > 2) {
        t = t.replace(/\./g, '');
      }
    }

    var x = parseFloat(t);
    return isNaN(x) ? 0 : x;
  }

  function cssEsc(s) {
    s = String(s || '');
    if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') return CSS.escape(s);
    return s.replace(/\\/g, '\\\\').replace(/"/g, '\\"');
  }

  function updatePromoPricingInScope(scope, boot, salePrice) {
    if (!scope || boot == null) return;

    scope.querySelectorAll('on-sale-badge').forEach(function (el) {
      el.remove();
    });

    var capRaw = boot.compareAtPrice != null ? Number(boot.compareAtPrice) : 0;
    var cap = isFinite(capRaw) ? capRaw : 0;
    var promo = cap > 0 && salePrice > 0 && cap > salePrice + 1e-3;

    scope.querySelectorAll('sale-price').forEach(function (sale) {
      var srLbl = promo ? 'İndirimli fiyat' : 'Fiyat';
      var sr = sale.querySelector('.sr-only');
      if (sr) sr.textContent = srLbl;

      sale.classList.toggle('text-on-sale', promo);
    });

    scope.querySelectorAll('compare-at-price').forEach(function (el) {
      el.style.display = promo ? '' : 'none';
    });
  }

  function slugFromUrunPath() {
    try {
      var p = decodeURIComponent(window.location.pathname || '');
      var m = p.match(/\/urun\/([^/]+)/);
      if (m) return m[1].replace(/\/+$/, '').trim();
      var m2 = p.match(/\/products\/([^/]+?)(?:\.html)?$/i);
      if (m2) return m2[1].replace(/\/+$/, '').trim();

      return '';
    } catch (ePath) {
      return '';
    }
  }

  function variantPickerForForm(form) {
    if (!form || !form.id) return null;
    return document.querySelector('variant-picker[form="' + cssEsc(form.id) + '"]');
  }

  function inferBootFromDom(form) {
    var slug = '';
    var title = '';
    var price = parseMoneyNearForm(form);
    var currency = 'TRY';
    var image = '';

    var vp = variantPickerForForm(form);
    if (vp) slug = (vp.getAttribute('handle') || '').trim();

    var drawer = form.closest('quick-buy-drawer');
    if (!slug && drawer) slug = (drawer.getAttribute('handle') || '').trim();

    if (drawer) {
      var ta = drawer.querySelector('.quick-buy-drawer__info a.bold, .v-stack a.bold');
      if (ta) title = (ta.textContent || '').trim();
    }
    if (!title) {
      var h = document.querySelector('.product-info__title, h1.product-info__title');
      if (h) title = (h.textContent || '').trim();
    }

    return {
      slug: slug,
      title: title,
      price: price,
      currency: currency,
      image: image,
      variants: [],
    };
  }

  function parseBootDataset(form) {
    var raw = form.getAttribute('data-ded-boot');
    if (!raw) return null;
    try {
      return JSON.parse(raw);
    } catch (e) {
      return null;
    }
  }

  function resolveBoot(form) {
    var ds = parseBootDataset(form);
    if (ds && ds.slug) return ds;
    if (window.DED_PRODUCT_BOOT && window.DED_PRODUCT_BOOT.slug) return window.DED_PRODUCT_BOOT;
    var inf = inferBootFromDom(form);
    if (inf && inf.slug) return inf;
    var pathSlug = slugFromUrunPath();
    if (pathSlug) {
      return Object.assign({}, window.DED_PRODUCT_BOOT || {}, inf || {}, { slug: pathSlug });
    }

    return window.DED_PRODUCT_BOOT || inf || {};
  }

  function getSelectElement(form) {
    if (!form || !form.elements) return null;
    function usable(sel) {
      if (!sel || sel.nodeName !== 'SELECT') return null;
      if (sel.closest && sel.closest('noscript')) return null;
      return sel;
    }
    var el = form.elements['id'];
    var one = usable(el && el.nodeName === 'SELECT' ? el : null);
    if (one) return one;
    if (el && el.length) {
      for (var i = 0; i < el.length; i++) {
        var u = usable(el[i]);
        if (u) return u;
      }
    }
    return null;
  }

  function pickOption1Value(form) {
    if (!form || !form.elements) return '';
    var el = form.elements['option1'];
    if (!el) return '';
    if (el.nodeName === 'INPUT') {
      if (el.type === 'radio') return el.checked ? String(el.value || '').trim() : '';
      if (el.type === 'hidden') return String(el.value || '').trim();
      return '';
    }
    if (el.length) {
      var i;
      for (i = 0; i < el.length; i++) {
        var r = el[i];
        if (r.nodeName === 'INPUT' && r.type === 'radio' && r.checked) return String(r.value || '').trim();
      }
      for (i = 0; i < el.length; i++) {
        var h = el[i];
        if (h.nodeName === 'INPUT' && h.type === 'hidden') return String(h.value || '').trim();
      }
    }
    return '';
  }

  function listboxOptionValues(vp) {
    var out = {};
    if (!vp) return out;
    var opts = vp.querySelectorAll('[role="option"]');
    var i;
    var v;
    var t;
    for (i = 0; i < opts.length; i++) {
      v = opts[i].getAttribute('value');
      if (v !== null && String(v).trim() !== '') out[String(v).trim()] = true;
      t = String(opts[i].textContent || '').trim();
      if (t) out[t] = true;
    }
    return out;
  }

  function hiddenOption1Raw(form) {
    if (!form || !form.elements) return '';
    var el = form.elements['option1'];
    if (el && el.nodeName === 'INPUT' && el.type === 'hidden') return String(el.value || '').trim();
    if (el && el.length) {
      var i;
      for (i = 0; i < el.length; i++) {
        if (el[i].nodeName === 'INPUT' && el[i].type === 'hidden') return String(el[i].value || '').trim();
      }
    }
    return '';
  }

  function reconcileOption1WithListbox(form, raw) {
    raw = String(raw || '').trim();
    if (!raw) return '';
    var vp = variantPickerForForm(form);
    var allowed = listboxOptionValues(vp);
    var keys = Object.keys(allowed);
    if (keys.length === 0) {
      return raw;
    }
    var i;
    for (i = 0; i < keys.length; i++) {
      if (keys[i] === raw) return raw;
    }
    return '';
  }

  function pickListboxSelectedValue(form) {
    var vp = variantPickerForForm(form);
    if (!vp) return '';
    var opts = vp.querySelectorAll('[role="option"]');
    var i;
    var o;
    var v;
    var want = hiddenOption1Raw(form);
    for (i = 0; i < opts.length; i++) {
      o = opts[i];
      if (o.getAttribute('aria-selected') === 'true' || o.getAttribute('aria-current') === 'true') {
        v = o.getAttribute('value');
        if (v !== null && String(v).trim() !== '') return String(v).trim();
        return String(o.textContent || '').trim();
      }
    }
    if (want) {
      for (i = 0; i < opts.length; i++) {
        o = opts[i];
        v = o.getAttribute('value');
        if (v !== null && String(v).trim() === want) return want;
        if (String(o.textContent || '').trim() === want) return want;
      }
    }
    for (i = 0; i < opts.length; i++) {
      o = opts[i];
      if (o.classList && o.classList.contains('is-selected')) {
        v = o.getAttribute('value');
        if (v !== null && String(v).trim() !== '') return String(v).trim();
        return String(o.textContent || '').trim();
      }
    }
    return '';
  }

  function variantLabelForUi(form, boot) {
    boot = boot || {};
    var sel = getSelectElement(form);
    if (sel && sel.selectedOptions && sel.selectedOptions[0]) {
      var opt = sel.selectedOptions[0];
      var t = (opt.textContent || '').trim();
      var dash = t.indexOf(' - ');
      if (dash > 0) return t.slice(0, dash).trim();
      if (t) return t;
      var idxSel = parseInt(opt.value, 10);
      if (!isNaN(idxSel) && boot.variants && boot.variants[idxSel])
        return String(boot.variants[idxSel].name || '').trim();
    }
    var lb = pickListboxSelectedValue(form);
    if (lb) return lb;
    var o1 = reconcileOption1WithListbox(form, pickOption1Value(form));
    if (o1) return o1;
    var vp = variantPickerForForm(form);
    if (vp) {
      var voc = vp.querySelector('variant-option-value');
      if (voc) {
        var vt = (voc.textContent || '').trim();
        if (vt && reconcileOption1WithListbox(form, vt)) return vt;
      }
      var trig = vp.querySelector('button.select span[id*="selected-value"]');
      if (trig) {
        var tx = (trig.textContent || '').trim();
        if (tx && reconcileOption1WithListbox(form, tx)) return tx;
      }
    }
    return '';
  }

  function syncOption1FromLabel(form, label) {
    if (!form || !form.elements) return;
    label = String(label || '').trim();
    if (!label) return;
    var el = form.elements['option1'];
    if (!el) return;
    if (el.nodeName === 'INPUT') {
      if (el.type === 'hidden') el.value = label;
      if (el.type === 'radio') el.checked = String(el.value || '').trim() === label;
      return;
    }
    if (el.length) {
      for (var i = 0; i < el.length; i++) {
        var n = el[i];
        if (n.nodeName !== 'INPUT') continue;
        if (n.type === 'hidden') n.value = label;
        if (n.type === 'radio') n.checked = String(n.value || '').trim() === label;
      }
    }
  }

  function variantIndexByName(boot, name) {
    name = String(name || '').trim();
    if (!name || !boot || !boot.variants || !boot.variants.length) return -1;
    for (var i = 0; i < boot.variants.length; i++) {
      if (String(boot.variants[i].name || '').trim() === name) return i;
    }
    return -1;
  }

  function pickHiddenIdValue(form) {
    if (!form || !form.elements) return '';
    var el = form.elements['id'];
    if (!el) return '';
    function val(inp) {
      if (!inp || inp.nodeName !== 'INPUT') return '';
      if (inp.type !== 'hidden') return '';
      if (inp.disabled) return '';
      return String(inp.value || '').trim();
    }
    var one = val(el);
    if (one !== '') return one;
    if (el.length) {
      for (var i = 0; i < el.length; i++) {
        var v = val(el[i]);
        if (v !== '') return v;
      }
    }
    return '';
  }

  function variantFromForm(form, boot) {
    boot = boot || {};

    var sel = getSelectElement(form);
    if (sel && sel.selectedOptions && sel.selectedOptions[0]) {
      var opt = sel.selectedOptions[0];
      var t = (opt.textContent || '').trim();
      var dash = t.indexOf(' - ');
      if (dash > 0) return t.slice(0, dash).trim();
      if (t) return t;
      var idxSel = parseInt(opt.value, 10);
      if (!isNaN(idxSel) && boot.variants && boot.variants[idxSel])
        return String(boot.variants[idxSel].name || '').trim();
    }

    var lb = pickListboxSelectedValue(form);
    if (lb) return lb;

    var o1 = reconcileOption1WithListbox(form, pickOption1Value(form));
    if (o1) return o1;

    var hid = pickHiddenIdValue(form);
    if (hid !== '') {
      var n = parseInt(hid, 10);
      if (!isNaN(n) && boot.variants && boot.variants[n] && boot.variants[n].name)
        return String(boot.variants[n].name || '').trim();
      if (hid.length > 8 && /^\d+$/.test(hid)) return '';
      if (!isNaN(n) && String(n) === hid && boot.variants && boot.variants[n]) {
        return String(boot.variants[n].name || '').trim();
      }
    }

    return '';
  }

  function variantIndexFromForm(form, boot) {
    boot = boot || {};

    var sel = getSelectElement(form);
    if (sel && sel.selectedOptions && sel.selectedOptions[0]) {
      var ixSel = parseInt(sel.selectedOptions[0].value, 10);
      if (!isNaN(ixSel) && ixSel >= 0) return ixSel;
    }

    var lb = pickListboxSelectedValue(form);
    if (lb) {
      var ixLb = variantIndexByName(boot, lb);
      if (ixLb >= 0) return ixLb;
    }

    var o1 = reconcileOption1WithListbox(form, pickOption1Value(form));
    if (o1) {
      var ixO = variantIndexByName(boot, o1);
      if (ixO >= 0) return ixO;
    }

    var hid = pickHiddenIdValue(form);
    if (hid !== '') {
      var n = parseInt(hid, 10);
      if (!isNaN(n) && n >= 0) return n;
    }

    return -1;
  }

  function variantPrice(form, boot) {
    boot = boot || {};

    var sel = getSelectElement(form);
    if (sel && sel.selectedOptions && sel.selectedOptions[0]) {
      var opt = sel.selectedOptions[0];
      var p = parseFloat(opt.getAttribute('data-ded-price') || '', 10);
      if (!isNaN(p) && p > 0) return p;
      var idx = parseInt(opt.value, 10);
      if (!isNaN(idx) && boot.variants && boot.variants[idx])
        return Number(boot.variants[idx].price) || Number(boot.price) || 0;
    }

    var name =
      pickListboxSelectedValue(form) || reconcileOption1WithListbox(form, pickOption1Value(form));
    var ix = variantIndexByName(boot, name);
    if (ix >= 0 && boot.variants[ix]) return Number(boot.variants[ix].price) || Number(boot.price) || 0;

    var hid = pickHiddenIdValue(form);
    var hi = parseInt(hid, 10);
    if (!isNaN(hi) && boot.variants && boot.variants[hi])
      return Number(boot.variants[hi].price) || Number(boot.price) || 0;

    var pm = parseMoneyNearForm(form);
    if (pm > 0) return pm;

    return Number(boot.price) || 0;
  }

  function syncSelectToVariantName(form, boot, name) {
    var sel = getSelectElement(form);
    if (!sel || !boot.variants) return;
    var ix = variantIndexByName(boot, name);
    if (ix < 0) return;
    for (var i = 0; i < sel.options.length; i++) {
      if (parseInt(sel.options[i].value, 10) === ix) {
        sel.selectedIndex = i;
        return;
      }
    }
  }

  function syncHiddenIdToIndex(form, boot, idx) {
    if (!form || !form.elements) return;
    var el = form.elements['id'];
    function set(inp) {
      if (!inp || inp.nodeName !== 'INPUT' || inp.type !== 'hidden') return;
      if (inp.disabled) return;
      inp.value = String(idx);
    }
    if (el && el.nodeName === 'INPUT') set(el);
    else if (el && el.length) {
      for (var i = 0; i < el.length; i++) set(el[i]);
    }
  }

  function syncVariantIdControl(form, boot, idx) {
    if (!form || !form.elements || isNaN(idx) || idx < 0) return;
    var el = form.elements['id'];
    function usableSelect(sel) {
      return sel && sel.nodeName === 'SELECT' && (!sel.closest || !sel.closest('noscript'));
    }
    var sel = usableSelect(el && el.nodeName === 'SELECT' ? el : null);
    if (!sel && el && el.length) {
      var j;
      for (j = 0; j < el.length; j++) {
        if (usableSelect(el[j])) {
          sel = el[j];
          break;
        }
      }
    }
    if (sel && sel.options && sel.options.length > 0) {
      var i;
      for (i = 0; i < sel.options.length; i++) {
        if (parseInt(sel.options[i].value, 10) === idx) {
          sel.selectedIndex = i;
          return;
        }
      }
      return;
    }
    syncHiddenIdToIndex(form, boot, idx);
  }

  function refreshUiForForm(form, boot, triggeredBy) {
    triggeredBy = triggeredBy || 'select';
    boot = boot || {};
    var sel = getSelectElement(form);

    if (triggeredBy === 'option1') {
      var o =
        pickListboxSelectedValue(form) || reconcileOption1WithListbox(form, pickOption1Value(form));
      if (o && sel) syncSelectToVariantName(form, boot, o);
    }

    var displayName = variantLabelForUi(form, boot);
    syncOption1FromLabel(form, displayName);

    var ix =
      sel && sel.selectedOptions && sel.selectedOptions[0]
        ? parseInt(sel.selectedOptions[0].value, 10)
        : variantIndexByName(boot, displayName);
    if (!isNaN(ix) && ix >= 0) syncVariantIdControl(form, boot, ix);

    updateDisplayedPrice(form, variantPrice(form, boot));

    var pvEl = null;
    var vp = variantPickerForForm(form);
    if (vp) pvEl = vp.querySelector('variant-option-value');
    if (!pvEl && form.id) {
      pvEl = document.querySelector('variant-option-value[form="' + cssEsc(form.id) + '"]');
    }
    if (pvEl && displayName) pvEl.textContent = displayName;

    if (vp && displayName) {
      vp.querySelectorAll('button.select span[id*="selected-value"]').forEach(function (sp) {
        sp.textContent = displayName;
      });
      vp.querySelectorAll('x-listbox[aria-owns]').forEach(function (lb) {
        var oid = lb.getAttribute('aria-owns');
        if (!oid) return;
        var node = document.getElementById(oid);
        if (node) node.textContent = displayName;
      });
      var sp2 = vp.querySelector('#popover-ded-selected');
      if (sp2) sp2.textContent = displayName;
    }
  }

  function updateDisplayedPrice(form, price) {
    function applySaleInner(saleEl, p) {
      var sr = saleEl.querySelector('.sr-only');
      var label = sr ? sr.outerHTML : '<span class="sr-only">İndirimli fiyat</span>';
      saleEl.innerHTML = label + formatTry(p);
    }

    var scope = form ? form.closest('.product-info, quick-buy-drawer, product-quick-add') : null;
    if (scope) {
      scope.querySelectorAll('sale-price').forEach(function (saleEl) {
        applySaleInner(saleEl, price);
      });
    } else {
      var one = document.querySelector('sale-price');
      if (one) applySaleInner(one, price);
    }
    var rb = form ? resolveBoot(form) : {};
    if (
      window.DED_PRODUCT_BOOT &&
      rb.slug &&
      rb.slug === window.DED_PRODUCT_BOOT.slug
    ) {
      window.DED_PRODUCT_BOOT.price = price;
    }
    if (scope) {
      updatePromoPricingInScope(scope, rb, price);
    }
  }

  function observeVariantPickerAttrs(form) {
    if (!form || form._dedVpObs) return;
    var vp = variantPickerForForm(form);
    if (!vp) return;
    form._dedVpObs = true;
    var obs = new MutationObserver(function () {
      refreshUiForForm(form, resolveBoot(form), 'option1');
    });
    obs.observe(vp, {
      subtree: true,
      attributes: true,
      attributeFilter: ['aria-selected', 'aria-current', 'value'],
    });
  }

  function observeHiddenOption1(form) {
    if (!form || form._dedObsOpt1) return;
    form._dedObsOpt1 = true;
    var el = form.elements['option1'];
    var list = [];
    if (el && el.nodeName === 'INPUT' && el.type === 'hidden') list.push(el);
    if (el && el.length) {
      for (var i = 0; i < el.length; i++) {
        if (el[i].nodeName === 'INPUT' && el[i].type === 'hidden') list.push(el[i]);
      }
    }
    list.forEach(function (inp) {
      var obs = new MutationObserver(function () {
        refreshUiForForm(form, resolveBoot(form), 'option1');
      });
      obs.observe(inp, { attributes: true, attributeFilter: ['value'] });
    });
  }

  function bindVariantChange() {
    document.querySelectorAll('select[name="id"]').forEach(function (sel) {
      if (sel._dedBound) return;
      sel._dedBound = true;
      sel.addEventListener('change', function () {
        var form = sel.form;
        if (!form) return;
        var boot = resolveBoot(form);
        refreshUiForForm(form, boot, 'select');
      });
    });

    document.querySelectorAll('input[type="radio"][name="option1"]').forEach(function (radio) {
      if (radio._dedBound) return;
      radio._dedBound = true;
      radio.addEventListener('change', function () {
        var form = radio.form;
        if (!form) return;
        var boot = resolveBoot(form);
        refreshUiForForm(form, boot, 'option1');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    bindVariantChange();
    document.querySelectorAll('form.shopify-product-form[is="product-form"]').forEach(function (form) {
      if (!form.action || form.action.indexOf('cart/add') === -1) return;
      observeHiddenOption1(form);
      observeVariantPickerAttrs(form);
      refreshUiForForm(form, resolveBoot(form), 'option1');
      if (variantPickerForForm(form)) {
        requestAnimationFrame(function () {
          refreshUiForForm(form, resolveBoot(form), 'option1');
          requestAnimationFrame(function () {
            refreshUiForForm(form, resolveBoot(form), 'option1');
          });
        });
        setTimeout(function () {
          refreshUiForForm(form, resolveBoot(form), 'option1');
        }, 350);
      }
    });
  });
  bindVariantChange();

  document.addEventListener(
    'click',
    function (ev) {
      var btn = ev.target.closest(
        '[data-ded-variant-idx], .popover-listbox__option[role="option"], button[data-option-value]'
      );
      if (!btn || !btn.closest('variant-picker')) return;
      var picker = btn.closest('variant-picker');
      var fid = picker && picker.getAttribute('form');
      var form = fid ? document.getElementById(fid) : null;
      if (!form) return;
      function sync() {
        refreshUiForForm(form, resolveBoot(form), 'option1');
      }
      sync();
      setTimeout(sync, 0);
      setTimeout(sync, 50);
    },
    true
  );

  document.addEventListener(
    'submit',
    function (ev) {
      var f = ev.target;
      if (!f || f.tagName !== 'FORM' || !f.action) return;
      if (f.action.indexOf('cart/add') === -1) return;
      if (f.getAttribute('is') !== 'product-form') return;

      var dedMode = typeof window.DED_VITRIN === 'object' && window.DED_VITRIN !== null;

      var boot = resolveBoot(f);
      if (!boot || !boot.slug) {
        if (dedMode) {
          ev.preventDefault();
          ev.stopPropagation();
        }
        return;
      }

      dedEnsureSepetCekirdek();

      var cart = window.DEDCart;
      if (!cart || typeof cart.add !== 'function') {
        if (dedMode) {
          ev.preventDefault();
          ev.stopPropagation();
        } else {
          try {
            HTMLFormElement.prototype.submit.call(f);
          } catch (eSub) {}
        }
        return;
      }

      ev.preventDefault();
      ev.stopPropagation();

      var qIn = f.querySelector('[name="quantity"]');
      var q = qIn ? parseInt(qIn.value, 10) : 1;
      if (isNaN(q) || q < 1) q = 1;

      var price = variantPrice(f, boot);
      var variantLabel = variantFromForm(f, boot);

      cart.add({
        slug: boot.slug,
        title: boot.title || '',
        price: price,
        currency: boot.currency || 'TRY',
        image: cart.resolveImage
          ? cart.resolveImage({ slug: boot.slug, image: boot.image || '' })
          : boot.image || '',
        variant: variantLabel,
        qty: q,
      });

      window.location.href = window.dedVitrinUrl ? window.dedVitrinUrl('cart') : 'sepet';
    },
    true
  );
})();
