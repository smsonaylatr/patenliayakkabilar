(function () {
  var KEY = 'yonetim-bs-theme';

  function applyTheme(theme) {
    if (theme !== 'dark' && theme !== 'light') {
      return;
    }
    document.documentElement.setAttribute('data-bs-theme', theme);
    try {
      localStorage.setItem(KEY, theme);
    } catch (e) {}
  }

  function readTheme() {
    return document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
  }

  try {
    var saved = localStorage.getItem(KEY);
    if (saved === 'dark' || saved === 'light') {
      applyTheme(saved);
    }
  } catch (e) {}

  var btn = document.getElementById('light-dark-mode');
  if (!btn) {
    return;
  }

  btn.addEventListener('click', function () {
    setTimeout(function () {
      applyTheme(readTheme());
    }, 0);
  });
})();

(function () {
  var nav = document.querySelector('.yun-sidebar-nav');
  if (!nav) {
    return;
  }

  nav.querySelectorAll('.yun-nav-group-toggle').forEach(function (toggle) {
    var target = toggle.getAttribute('href');
    if (!target || target.charAt(0) !== '#') {
      return;
    }
    var panel = document.querySelector(target);
    if (!panel) {
      return;
    }
    panel.addEventListener('show.bs.collapse', function () {
      toggle.classList.remove('collapsed');
      toggle.setAttribute('aria-expanded', 'true');
    });
    panel.addEventListener('hide.bs.collapse', function () {
      toggle.classList.add('collapsed');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });
})();
