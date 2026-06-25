(function () {
  'use strict';

  var ICON_CART =
    '<svg role="presentation" stroke-width="1.5" width="28" height="28" viewBox="0 0 22 22" fill="none" stroke="currentColor">' +
    '<path d="M11 7H3.577A2 2 0 0 0 1.64 9.497l2.051 8A2 2 0 0 0 5.63 19H16.37a2 2 0 0 0 1.937-1.503l2.052-8A2 2 0 0 0 18.422 7H11Zm0 0V1" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

  var ICON_SHIELD =
    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';

  var ICON_TRUCK =
    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>';

  var ICON_RETURN =
    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>';

  var ICON_CARD =
    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>';

  function esc(s) {
    var d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function url(route, params) {
    params = params || {};
    if (window.dedVitrinUrl) {
      if (route === 'product' || route === 'collection') {
        return window.dedVitrinUrl(route, params.slug || '');
      }
      if (route === 'orderSuccess') {
        return window.dedVitrinUrl(route, params.num || params.no || '');
      }
      return window.dedVitrinUrl(route);
    }
    var slug = params.slug ? encodeURIComponent(params.slug) : '';
    var map = {
      collections: 'koleksiyonlar',
      checkout: 'odeme',
      home: '',
      product: 'urun/' + slug,
      collection: 'koleksiyon/' + slug,
    };
    return map[route] || route;
  }

  function money(n) {
    if (!window.DEDCart) return '';
    return DEDCart.fmtTry(n).replace(/TL$/, ' ₺');
  }

  function itemCount(lines) {
    return lines.reduce(function (s, l) {
      return s + (parseInt(l.qty, 10) || 0);
    }, 0);
  }

  function trustBlock() {
    return (
      '<div class="ded-cart-trust">' +
      '<div class="ded-cart-trust__item">' + ICON_SHIELD + '<span>Güvenli ödeme</span></div>' +
      '<div class="ded-cart-trust__item">' + ICON_CARD + '<span>Kart & havale</span></div>' +
      '<div class="ded-cart-trust__item">' + ICON_TRUCK + '<span>Hızlı kargo</span></div>' +
      '<div class="ded-cart-trust__item">' + ICON_RETURN + '<span>Kolay iade</span></div>' +
      '</div>'
    );
  }

  function emptyHtml() {
    return (
      '<div class="ded-cart-empty">' +
      '<div class="ded-cart-empty__icon">' + ICON_CART + '</div>' +
      '<h2 class="ded-cart-empty__title">Sepetiniz boş</h2>' +
      '<p class="ded-cart-empty__text">Henüz ürün eklemediniz. Koleksiyonlarımıza göz atın; çocuklar için özel tekerlekli ayakkabı modellerini keşfedin.</p>' +
      '<a class="button button--xl" href="' + esc(url('collections')) + '">Alışverişe başla</a>' +
      '</div>'
    );
  }

  function lineHtml(line, i) {
    var q = parseInt(line.qty, 10) || 1;
    var price = Number(line.price) || 0;
    var imgSrc = window.DEDCart ? DEDCart.resolveImage(line) : '';
    var media = imgSrc
      ? '<a class="ded-cart-line__media" href="' + esc(url('product', { slug: line.slug })) + '"><img src="' + esc(imgSrc) + '" alt="" width="200" height="200" loading="lazy"></a>'
      : '<div class="ded-cart-line__media ded-cart-line__media--empty" aria-hidden="true"></div>';
    var variant = line.variant
      ? '<span class="ded-cart-line__variant">' + esc(line.variant) + '</span>'
      : '';

    return (
      '<article class="ded-cart-line" data-ded-line="' + i + '">' +
      media +
      '<div class="ded-cart-line__body">' +
      '<div class="ded-cart-line__top">' +
      '<a class="ded-cart-line__title" href="' + esc(url('product', { slug: line.slug })) + '">' + esc(line.title) + '</a>' +
      '<button type="button" class="ded-cart-line__remove" data-ded-remove="' + i + '" aria-label="Ürünü kaldır">Kaldır</button>' +
      '</div>' +
      variant +
      '<div class="ded-cart-line__unit">' + money(price) + ' / adet</div>' +
      '<div class="ded-cart-line__foot">' +
      '<div class="ded-cart-qty" role="group" aria-label="Adet">' +
      '<button type="button" class="ded-cart-qty__btn" data-ded-qty-minus="' + i + '"' + (q <= 1 ? ' disabled' : '') + ' aria-label="Azalt">−</button>' +
      '<span class="ded-cart-qty__val" data-ded-qty-display="' + i + '">' + q + '</span>' +
      '<button type="button" class="ded-cart-qty__btn" data-ded-qty-plus="' + i + '" aria-label="Artır">+</button>' +
      '</div>' +
      '<span class="ded-cart-line__total" data-ded-line-total="' + i + '">' + money(price * q) + '</span>' +
      '</div>' +
      '</div>' +
      '</article>'
    );
  }

  function summaryHtml(total, count) {
    return (
      '<div class="ded-cart-summary">' +
      '<h2 class="ded-cart-summary__title">Sipariş özeti</h2>' +
      '<div class="ded-cart-summary__rows">' +
      '<div class="ded-cart-summary__row"><span>Ürün (' + count + ')</span><span>' + money(total) + '</span></div>' +
      '<div class="ded-cart-summary__row"><span>Kargo</span><span>Ödemede hesaplanır</span></div>' +
      '<div class="ded-cart-summary__row ded-cart-summary__row--total"><span>Toplam</span><span data-ded-cart-grand-total>' + money(total) + '</span></div>' +
      '</div>' +
      '<p class="ded-cart-summary__note">Ödeme adımında iletişim ve adres bilgilerinizi girin. Havale/EFT talimatları sipariş onayından sonra paylaşılır.</p>' +
      '<div class="ded-cart-summary__actions">' +
      '<a class="button button--xl" href="' + esc(url('checkout')) + '">Ödemeye geç</a>' +
      '<a class="button button--secondary" href="' + esc(url('collections')) + '">Alışverişe devam et</a>' +
      '</div>' +
      '<button type="button" class="ded-cart-summary__clear" id="ded-cart-clear">Sepeti temizle</button>' +
      trustBlock() +
      '</div>'
    );
  }

  function updateHead(count) {
    var sub = document.getElementById('ded-cart-head-count');
    if (!sub) return;
    if (count === 0) {
      sub.textContent = 'Sepetinizde ürün yok';
    } else if (count === 1) {
      sub.textContent = '1 ürün';
    } else {
      sub.textContent = count + ' ürün';
    }
  }

  function bindEvents() {
    var root = document.getElementById('ded-cart-root');
    if (!root) return;

    root.querySelectorAll('[data-ded-remove]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        DEDCart.removeIndex(parseInt(btn.getAttribute('data-ded-remove'), 10));
        render();
      });
    });

    root.querySelectorAll('[data-ded-qty-minus]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var i = parseInt(btn.getAttribute('data-ded-qty-minus'), 10);
        var lines = DEDCart.get();
        var q = (parseInt(lines[i].qty, 10) || 1) - 1;
        if (q < 1) return;
        DEDCart.setQty(i, q);
        render();
      });
    });

    root.querySelectorAll('[data-ded-qty-plus]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var i = parseInt(btn.getAttribute('data-ded-qty-plus'), 10);
        var lines = DEDCart.get();
        var q = (parseInt(lines[i].qty, 10) || 1) + 1;
        DEDCart.setQty(i, q);
        render();
      });
    });

    var clr = document.getElementById('ded-cart-clear');
    if (clr) {
      clr.addEventListener('click', function () {
        if (window.confirm('Sepetteki tüm ürünler kaldırılsın mı?')) {
          DEDCart.clear();
          render();
        }
      });
    }
  }

  function syncHeader(count) {
    document.querySelectorAll('cart-count').forEach(function (el) {
      el.textContent = String(count);
      if (count > 0) el.classList.remove('opacity-0');
      else el.classList.add('opacity-0');
    });
  }

  function render() {
    var root = document.getElementById('ded-cart-root');
    var summary = document.getElementById('ded-cart-summary');
    var grid = document.querySelector('.ded-cart-page__grid');
    if (!root || !window.DEDCart) return;

    var lines = DEDCart.get();
    var count = itemCount(lines);

    updateHead(count);
    syncHeader(count);

    if (!lines.length) {
      if (grid) grid.classList.remove('ded-cart-page__grid--filled');
      root.innerHTML = emptyHtml();
      if (summary) {
        summary.innerHTML = '';
        summary.hidden = true;
      }
      return;
    }

    var total = 0;
    lines.forEach(function (line) {
      total += (Number(line.price) || 0) * (parseInt(line.qty, 10) || 1);
    });

    if (grid) grid.classList.add('ded-cart-page__grid--filled');
    root.innerHTML = '<div class="ded-cart-lines">' + lines.map(lineHtml).join('') + '</div>';

    if (summary) {
      summary.hidden = false;
      summary.innerHTML = summaryHtml(total, count);
    }

    bindEvents();
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.DEDCart && DEDCart.syncImagesFromMap) {
      DEDCart.syncImagesFromMap();
    }
    render();
  });
})();
