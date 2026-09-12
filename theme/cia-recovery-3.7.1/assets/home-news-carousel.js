(() => {
  'use strict';
  const root = document.querySelector('[data-news-carousel]');
  if (!root) return;
  const track = root.querySelector('[data-news-carousel-track]');
  const slides = [...root.querySelectorAll('[data-news-carousel-slide]')];
  const previous = root.querySelector('[data-news-carousel-prev]');
  const next = root.querySelector('[data-news-carousel-next]');
  const status = root.querySelector('[data-news-carousel-status]');
  const controls = root.querySelector('.home-news-controls');
  if (!track || slides.length < 2 || !previous || !next || !status || !controls) return;

  controls.removeAttribute("hidden");

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let activeIndex = 0;
  let frame = 0;

  const positionFor = (slide) => slide.offsetLeft - track.offsetLeft;
  const nearestIndex = () => {
    let nearest = 0;
    let distance = Infinity;
    slides.forEach((slide, index) => {
      const delta = Math.abs(positionFor(slide) - track.scrollLeft);
      if (delta < distance) { distance = delta; nearest = index; }
    });
    return nearest;
  };
  const update = (index = nearestIndex()) => {
    activeIndex = Math.max(0, Math.min(index, slides.length - 1));
    slides.forEach((slide, slideIndex) => {
      if (slideIndex === activeIndex) slide.setAttribute('aria-current', 'true');
      else slide.removeAttribute('aria-current');
    });
    previous.disabled = activeIndex === 0;
    next.disabled = activeIndex === slides.length - 1;
    status.textContent = `${activeIndex + 1} / ${slides.length}`;
  };
  const goTo = (index) => {
    const targetIndex = Math.max(0, Math.min(index, slides.length - 1));
    track.scrollTo({left: positionFor(slides[targetIndex]), behavior: reducedMotion.matches ? 'auto' : 'smooth'});
    update(targetIndex);
  };
  const queueUpdate = () => {
    if (frame) return;
    frame = requestAnimationFrame(() => { frame = 0; update(); });
  };

  previous.addEventListener('click', () => goTo(activeIndex - 1));
  next.addEventListener('click', () => goTo(activeIndex + 1));
  track.addEventListener('scroll', queueUpdate, {passive: true});
  track.addEventListener('keydown', (event) => {
    if (event.target !== track) return;
    const keys = {ArrowLeft: activeIndex - 1, ArrowRight: activeIndex + 1, Home: 0, End: slides.length - 1};
    if (!(event.key in keys)) return;
    event.preventDefault();
    goTo(keys[event.key]);
  });
  window.addEventListener('resize', queueUpdate, {passive: true});
  update(0);
})();
