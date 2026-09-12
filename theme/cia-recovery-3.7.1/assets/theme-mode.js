(function () {
  'use strict';
  var key = 'cia-theme-mode';
  var root = document.documentElement;
  var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
  var allowed = ['system', 'light', 'dark'];
  var stored = null;
  try { stored = localStorage.getItem(key); } catch (error) { stored = null; }
  var mode = allowed.indexOf(stored) >= 0 ? stored : 'system';

  function resolve(next) {
    if (next !== 'system') return next;
    return media && media.matches ? 'dark' : 'light';
  }

  function syncButtons() {
    document.querySelectorAll('[data-theme-mode]').forEach(function (button) {
      var active = button.getAttribute('data-theme-mode') === mode;
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }

  function apply(next) {
    mode = allowed.indexOf(next) >= 0 ? next : 'system';
    var effective = resolve(mode);
    root.setAttribute('data-theme', effective);
    root.setAttribute('data-theme-mode', mode);
    root.style.colorScheme = effective;
    syncButtons();
    try { localStorage.setItem(key, mode); } catch (error) {}
  }

  document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-theme-mode]');
    if (!button) return;
    apply(button.getAttribute('data-theme-mode'));
    var menu = button.closest('.header-theme-menu');
    if (menu) menu.removeAttribute('open');
  });

  document.addEventListener('DOMContentLoaded', syncButtons);
  if (media && media.addEventListener) {
    media.addEventListener('change', function () {
      if (mode === 'system') apply('system');
    });
  }
  apply(mode);
})();
