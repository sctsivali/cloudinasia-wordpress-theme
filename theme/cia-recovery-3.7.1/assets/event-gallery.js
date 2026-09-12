(() => {
  document.querySelectorAll('[data-cia-event-gallery]').forEach((gallery) => {
    const viewport = gallery.querySelector('[data-cia-gallery-viewport]');
    const slides = [...gallery.querySelectorAll('[data-cia-gallery-slide]')];
    const previous = gallery.querySelector('[data-cia-gallery-prev]');
    const next = gallery.querySelector('[data-cia-gallery-next]');
    const status = gallery.querySelector('[data-cia-gallery-status]');
    if (!viewport || slides.length < 2 || !previous || !next || !status) return;

    previous.hidden = false;
    next.hidden = false;

    const activeIndex = () => Math.max(0, Math.min(slides.length - 1, Math.round(viewport.scrollLeft / Math.max(1, viewport.clientWidth))));
    const update = () => {
      const index = activeIndex();
      previous.disabled = index === 0;
      next.disabled = index === slides.length - 1;
      status.textContent = `${index + 1} / ${slides.length}`;
    };
    const move = (direction) => viewport.scrollBy({ left: direction * viewport.clientWidth, behavior: 'smooth' });

    previous.addEventListener('click', () => move(-1));
    next.addEventListener('click', () => move(1));
    viewport.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    update();
  });
})();
