
(function () {
  var PANEL_IDS = ['sec-temel', 'sec-fiyat', 'sec-koleksiyon', 'sec-gorsel', 'sec-varyant', 'sec-seo'];

  function showTab(panelId) {
    if (PANEL_IDS.indexOf(panelId) < 0) {
      panelId = 'sec-temel';
    }
    var form = document.getElementById('yun-product-form');
    var nav = document.getElementById('yun-product-tabs');
    if (!form || !nav) {
      return;
    }
    PANEL_IDS.forEach(function (id) {
      var panel = document.getElementById(id);
      if (!panel) {
        return;
      }
      if (id === panelId) {
        panel.removeAttribute('hidden');
      } else {
        panel.setAttribute('hidden', '');
      }
    });
    nav.querySelectorAll('.yun-tab-btn').forEach(function (btn) {
      var on = btn.getAttribute('data-panel') === panelId;
      btn.classList.toggle('is-active', on);
    });
    try {
      var hash = '#' + panelId;
      if (location.hash !== hash) {
        history.replaceState(null, '', location.pathname + location.search + hash);
      }
    } catch (e) {
      location.hash = panelId;
    }
  }

  function bind() {
    var nav = document.getElementById('yun-product-tabs');
    if (!nav) {
      return;
    }
    nav.querySelectorAll('.yun-tab-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-panel');
        if (id) {
          showTab(id);
        }
      });
    });
    window.addEventListener('hashchange', function () {
      var id = (location.hash || '').replace(/^#/, '');
      if (id) {
        showTab(id);
      }
    });
    var fromHash = (location.hash || '').replace(/^#/, '');
    showTab(fromHash || 'sec-temel');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bind);
  } else {
    bind();
  }

  window.yunProductShowTab = showTab;
})();
