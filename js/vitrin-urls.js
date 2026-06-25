(function () {
  'use strict';

  function vitrin() {
    return window.DED_VITRIN || { base: '', collections: 'koleksiyonlar', cart: 'sepet', checkout: 'odeme' };
  }

  window.dedVitrinUrl = function (route, slug) {
    var v = vitrin();
    switch (route) {
      case 'product':
        return v.base + '/urun/' + encodeURIComponent(slug || '');
      case 'collection':
        return v.base + '/koleksiyon/' + encodeURIComponent(slug || '');
      case 'collections':
        return v.collections || v.base + '/koleksiyonlar';
      case 'cart':
        return v.cart || v.base + '/sepet';
      case 'checkout':
        return v.checkout || v.base + '/odeme';
      case 'orderSuccess':
        var u = v.orderSuccess || v.base + '/siparis-tamam';
        if (slug) {
          u += (u.indexOf('?') >= 0 ? '&' : '?') + 'num=' + encodeURIComponent(slug);
        }
        return u;
      default:
        return v.home || v.base + '/';
    }
  };
})();
