function prefersReducedMotion() {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function initSmoothScroll() {
  const links = document.querySelectorAll('[data-smooth-scroll]');

  if (!links.length) {
    return;
  }

  links.forEach((link) => {
    link.addEventListener('click', (event) => {
      const href = link.getAttribute('href');

      if (!href || !href.startsWith('#')) {
        return;
      }

      const targetId = href.slice(1);
      const target = document.getElementById(targetId);

      if (!target) {
        return;
      }

      event.preventDefault();

      target.scrollIntoView({
        behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        block: 'start',
      });

      history.replaceState(null, '', `#${targetId}`);
    });
  });
}

