(() => {
  const root = document.documentElement;
  const header = document.querySelector('.cia-chrome-header');
  let observedAdminBar = null;
  let resizeObserver = null;

  const updateStickyOffset = () => {
    if (!header) return;
    const adminBar = document.getElementById('wpadminbar');
    const headerHeight = header.getBoundingClientRect().height;
    const adminHeight = adminBar && getComputedStyle(adminBar).display !== 'none'
      ? adminBar.getBoundingClientRect().height
      : 0;
    const breathing = parseFloat(getComputedStyle(root).getPropertyValue('--cia-sticky-breathing')) || 16;
    document.body.style.setProperty('--cia-live-admin-bar-height', `${Math.ceil(adminHeight)}px`);
    root.style.setProperty('--cia-sticky-chrome-offset', `${Math.ceil(headerHeight + adminHeight + breathing)}px`);
  };

  const observeAdminBar = () => {
    const adminBar = document.getElementById('wpadminbar');
    if (resizeObserver && observedAdminBar && observedAdminBar !== adminBar) {
      resizeObserver.unobserve(observedAdminBar);
    }
    if (resizeObserver && adminBar && observedAdminBar !== adminBar) {
      resizeObserver.observe(adminBar);
    }
    observedAdminBar = adminBar;
    updateStickyOffset();
  };

  if (header) {
    if ('ResizeObserver' in window) {
      resizeObserver = new ResizeObserver(updateStickyOffset);
      resizeObserver.observe(header);
    }
    const startOffsetTracking = () => observeAdminBar();
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', startOffsetTracking, { once: true });
    } else {
      startOffsetTracking();
    }
    if ('MutationObserver' in window) {
      new MutationObserver(observeAdminBar).observe(document.body, { childList: true });
    }
    window.addEventListener('resize', observeAdminBar, { passive: true });
  }

  const copyFallback = (value) => {
    const field = document.createElement('textarea');
    try {
      field.value = value;
      field.setAttribute('readonly', '');
      field.style.position = 'fixed';
      field.style.opacity = '0';
      document.body.appendChild(field);
      field.select();
      if (!document.execCommand('copy')) throw new Error('copy command failed');
    } finally {
      field.remove();
    }
  };

  const writeClipboard = async (value) => {
    if (navigator.clipboard && window.isSecureContext) {
      try {
        await navigator.clipboard.writeText(value);
      } catch (error) {
        copyFallback(value);
      }
    } else {
      copyFallback(value);
    }
  };

  document.querySelectorAll('.article-copy-link[data-share-url]').forEach((button) => {
    button.addEventListener('click', async () => {
      const value = button.dataset.shareUrl || window.location.href;
      const status = document.getElementById(button.getAttribute('aria-describedby'));
      const manual = document.getElementById('article-copy-manual');
      if (manual) manual.hidden = true;
      try {
        await writeClipboard(value);
        if (status) status.textContent = status.dataset.copySuccess || 'Link copied';
      } catch (error) {
        if (status) status.textContent = status.dataset.copyFailed || 'Automatic copy failed. Copy the link manually.';
        if (manual) {
          manual.value = value;
          manual.hidden = false;
          manual.focus();
          manual.select();
        }
      }
    });
  });
})();
