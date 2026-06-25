
(function () {
  'use strict';
  if (window.__dedHizliekleInit) return;
  window.__dedHizliekleInit = true;

  function prefUrl(handle) {
    var pfx = window.DED_VITRIN && window.DED_VITRIN.productHrefPrefix;
    if (!pfx || typeof pfx !== 'string') return null;
    var h = String(handle || '').replace(/^\/+|\/$/g, '');
    if (!h) return null;
    return String(pfx).replace(/\/+$/, '') + '/' + encodeURIComponent(h).replace(/%2C/gi, ',');
  }

  function patchVariantPicker() {
    var VP = window.customElements.get('variant-picker');
    if (!VP || VP.prototype.__dedDedVpQk) return;
    VP.prototype.__dedDedVpQk = true;
    var origCb = VP.prototype.connectedCallback;
    VP.prototype.connectedCallback = function () {
      if (
        this.closest &&
        this.closest('quick-buy-drawer') &&
        String(this.getAttribute('data-ded-native-picker') || '').trim() === '1'
      ) {
        return;
      }
      return typeof origCb === 'function' ? origCb.apply(this, arguments) : undefined;
    };
  }

  function patchQuickBuyDrawer() {
    var Q = window.customElements.get('quick-buy-drawer');
    if (!Q || Q.prototype.__dedDedQbQk) return;
    Q.prototype.__dedDedQbQk = true;
    var origShow = Q.prototype.show;
    Q.prototype.show = async function dedQuickBuyShow() {
      try {
        var handle = String(this.getAttribute('handle') || '');
        var pref = prefUrl(handle);
        if (pref && this.__dedQbForHandle !== handle) {
          this.replaceChildren();
          this._hasLoaded = false;
        }
        if (pref && !this._hasLoaded) {
          var busy = [this].concat(Array.from(this.controls || []));
          busy.forEach(function (c) {
            c.setAttribute('aria-busy', 'true');
          });
          try {
            var responseContent = await (await fetch(pref, { credentials: 'same-origin' })).text();
            var doc = new DOMParser().parseFromString(responseContent, 'text/html');
            var tpl = doc.getElementById('quick-buy-content');
            if (tpl && tpl.content) {
              var qb = tpl.content.cloneNode(true);
              Array.from(qb.querySelectorAll('noscript')).forEach(function (n) {
                n.remove();
              });
              this.replaceChildren(qb);
              if (typeof window.Shopify !== 'undefined' && window.Shopify.PaymentButton) {
                try {
                  window.Shopify.PaymentButton.init();
                } catch (ePb) {}
              }
              this._hasLoaded = true;
              this.__dedQbForHandle = handle;
            }
          } finally {
            busy.forEach(function (c) {
              c.setAttribute('aria-busy', 'false');
            });
          }
        }
      } catch (_eDed) {}

      return origShow.apply(this, arguments);
    };
  }

  function runPatches() {
    patchVariantPicker();
    patchQuickBuyDrawer();
  }

  if (!window.customElements || !window.customElements.whenDefined) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', runPatches);
    } else runPatches();
    return;
  }

  Promise.all([
    customElements.whenDefined('variant-picker').catch(function () {}),
    customElements.whenDefined('quick-buy-drawer').catch(function () {}),
  ]).then(runPatches);

  window.addEventListener('load', function () {
    if (!window.customElements || !window.customElements.get('quick-buy-drawer')) return;
    setTimeout(runPatches, 0);
  });
})();
