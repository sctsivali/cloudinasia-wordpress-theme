(function () {
  'use strict';
  const documentRoot = document.documentElement;
  const chromeHeader = document.querySelector('.cia-chrome-header');

  function updateChromeGeometry() {
    if (!chromeHeader) return;
    const adminBar = document.getElementById('wpadminbar');
    const adminHeight = adminBar && getComputedStyle(adminBar).display !== 'none'
      ? adminBar.getBoundingClientRect().height
      : 0;
    const headerHeight = chromeHeader.getBoundingClientRect().height;
    const breathing = parseFloat(getComputedStyle(documentRoot).getPropertyValue('--cia-sticky-breathing')) || 16;
    const edge = Math.ceil(adminHeight + headerHeight);
    documentRoot.style.setProperty('--cia-sticky-chrome-edge', `${edge}px`);
    documentRoot.style.setProperty('--cia-sticky-chrome-offset', `${Math.ceil(edge + breathing)}px`);
  }

  if (chromeHeader) {
    updateChromeGeometry();
    window.addEventListener('resize', updateChromeGeometry, {passive: true});
    if ('ResizeObserver' in window) {
      const chromeObserver = new ResizeObserver(updateChromeGeometry);
      chromeObserver.observe(chromeHeader);
      const adminBar = document.getElementById('wpadminbar');
      if (adminBar) chromeObserver.observe(adminBar);
    }
  }

  const drawer = document.getElementById('cia-mobile-drawer');
  const opener = document.getElementById('cia-menu-open');
  const closer = document.getElementById('cia-menu-close');
  const backToTop = document.getElementById('back-to-top');
  if (!drawer || !opener || !closer) return;

  const focusable = 'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])';
  let lastFocus = null;

  function closeDrawer(restoreFocus) {
    if (drawer.hidden) return;
    drawer.dataset.open = 'false';
    drawer.hidden = true;
    opener.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('cia-menu-open');
    if (restoreFocus !== false && lastFocus) lastFocus.focus();
  }

  function openDrawer() {
    lastFocus = document.activeElement;
    drawer.hidden = false;
    drawer.dataset.open = 'true';
    opener.setAttribute('aria-expanded', 'true');
    document.body.classList.add('cia-menu-open');
    closer.focus();
  }

  opener.addEventListener('click', openDrawer);
  closer.addEventListener('click', function () { closeDrawer(true); });
  drawer.querySelectorAll('a[href]').forEach(function (link) {
    link.addEventListener('click', function () { closeDrawer(false); });
  });

  document.addEventListener('keydown', function (event) {
    if (drawer.hidden) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      closeDrawer(true);
      return;
    }
    if (event.key !== 'Tab') return;
    const nodes = Array.from(drawer.querySelectorAll(focusable)).filter(function (node) {
      return node.offsetParent !== null;
    });
    if (!nodes.length) return;
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });

  const desktop = window.matchMedia('(min-width: 900px)');
  function closeAtDesktop(event) { if (event.matches) closeDrawer(false); }
  if (desktop.addEventListener) desktop.addEventListener('change', closeAtDesktop);
  else desktop.addListener(closeAtDesktop);

  if (backToTop) {
    backToTop.addEventListener('click', function () {
      const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      window.scrollTo({top: 0, behavior: reduced ? 'auto' : 'smooth'});
    });
  }
}());
