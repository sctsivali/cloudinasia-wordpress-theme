(function () {
  'use strict';
  var target = document.querySelector('[data-cia-lottie]');
  if (!target || !window.lottie) return;

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var animation;
  try {
    animation = window.lottie.loadAnimation({
      container: target,
      renderer: 'svg',
      loop: !reduced,
      autoplay: !reduced,
      path: target.getAttribute('data-animation-path'),
      rendererSettings: {
        progressiveLoad: true,
        preserveAspectRatio: 'xMidYMid meet',
        title: 'Southeast Asia editorial signal map',
        description: 'Natural Earth map of Southeast Asia with an animated editorial route connecting Jakarta, Singapore, Kuala Lumpur, Bangkok, Ho Chi Minh City, and Manila.'
      }
    });
  } catch (error) {
    target.classList.add('is-lottie-failed');
    return;
  }

  animation.addEventListener('DOMLoaded', function () {
    if (target.parentNode) target.parentNode.classList.add('is-lottie-ready');
    if (reduced) animation.goToAndStop(90, true);
  });
  animation.addEventListener('data_failed', function () {
    target.classList.add('is-lottie-failed');
    if (target.parentNode) target.parentNode.classList.remove('is-lottie-ready');
  });

  document.addEventListener('visibilitychange', function () {
    if (reduced) return;
    if (document.hidden) animation.pause(); else animation.play();
  });
})();
