(() => {
  'use strict';
  const frame = document.querySelector('[data-blog-motion-frame]');
  const mount = frame && frame.querySelector('[data-blog-lottie]');
  if (!frame || !mount) return;

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  let fallbackTimer = null;
  const fallback = () => {
    if (fallbackTimer) window.clearTimeout(fallbackTimer);
    frame.classList.remove('is-loading', 'is-ready');
    frame.classList.add('is-fallback');
  };
  if (reduced.matches) {
    frame.classList.add('is-reduced');
    fallback();
    return;
  }
  if (!window.lottie || typeof window.lottie.loadAnimation !== 'function') {
    fallback();
    return;
  }

  let animation;
  try {
    fallbackTimer = window.setTimeout(fallback, 3500);
    animation = window.lottie.loadAnimation({
      container: mount,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: mount.dataset.animationPath,
      rendererSettings: { preserveAspectRatio: 'xMidYMid meet', progressiveLoad: false }
    });
    animation.addEventListener('DOMLoaded', () => {
      if (fallbackTimer) window.clearTimeout(fallbackTimer);
      frame.classList.remove('is-loading', 'is-fallback');
      frame.classList.add('is-ready');
    });
    animation.addEventListener('data_failed', fallback);
    animation.addEventListener('error', fallback);
    reduced.addEventListener('change', event => {
      if (!event.matches) return;
      animation.stop();
      animation.destroy();
      frame.classList.add('is-reduced');
      fallback();
    }, { once: true });
  } catch (error) {
    fallback();
  }
})();
