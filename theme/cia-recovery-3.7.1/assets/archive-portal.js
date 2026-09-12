(() => {
  const root = document.querySelector('[data-archive-portal]');
  if (!root) return;
  root.classList.add('has-archive-motion');
  const archiveFilterBar = root.querySelector('.archive-portal-filters');
  const chromeHeader = document.querySelector('.cia-chrome-header');
  const documentRoot = document.documentElement;

  const updateArchiveGeometry = () => {
    const adminBar = document.getElementById('wpadminbar');
    const adminHeight = adminBar && getComputedStyle(adminBar).display !== 'none'
      ? adminBar.getBoundingClientRect().height
      : 0;
    const headerHeight = chromeHeader ? chromeHeader.getBoundingClientRect().height : 0;
    const filterHeight = archiveFilterBar ? archiveFilterBar.getBoundingClientRect().height : 0;
    const breathing = parseFloat(getComputedStyle(documentRoot).getPropertyValue('--cia-sticky-breathing')) || 16;
    const chromeEdge = Math.ceil(adminHeight + headerHeight);
    root.style.setProperty('--cia-archive-sticky-offset', `${Math.ceil(chromeEdge + filterHeight + breathing)}px`);
  };

  updateArchiveGeometry();
  window.addEventListener('resize', updateArchiveGeometry, {passive: true});
  if ('ResizeObserver' in window) {
    const archiveGeometryObserver = new ResizeObserver(updateArchiveGeometry);
    if (chromeHeader) archiveGeometryObserver.observe(chromeHeader);
    if (archiveFilterBar) archiveGeometryObserver.observe(archiveFilterBar);
  }

  const cards = [...root.querySelectorAll('[data-archive-card]')];
  const filters = [...root.querySelectorAll('[data-archive-filter]')];
  const count = root.querySelector('[data-archive-result-count]');
  const more = root.querySelector('[data-archive-load-more]');
  const countLabel = root.dataset.countLabel || '';
  let active = 'all';
  let visibleLimit = 6;

  const render = () => {
    let matches = 0;
    cards.forEach(card => {
      const topics = (card.dataset.topics || '').split(/\s+/);
      const matched = active === 'all' || topics.includes(active);
      if (matched) matches += 1;
      const shown = matched && matches <= visibleLimit;
      card.hidden = !shown;
      card.classList.toggle('is-pending', matched && !shown);
    });
    if (count) count.textContent = `${matches} ${countLabel}`.trim();
    if (more) more.hidden = matches <= visibleLimit;
  };

  filters.forEach(button => button.addEventListener('click', () => {
    active = button.dataset.archiveFilter || 'all';
    visibleLimit = 6;
    filters.forEach(item => {
      const selected = item === button;
      item.classList.toggle('is-active', selected);
      item.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });
    render();
  }));

  if (more) more.addEventListener('click', () => {
    visibleLimit += 6;
    render();
  });

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const reveal = [...root.querySelectorAll('[data-reveal]')];
  if (reduced || !('IntersectionObserver' in window)) {
    reveal.forEach(item => item.classList.add('is-visible'));
  } else {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08 });
    reveal.forEach(item => observer.observe(item));
  }
  render();
})();
