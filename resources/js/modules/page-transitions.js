const LEAVE_ANIMATION_CLASS = 'page-is-leaving';
const LEAVE_DURATION_MS = 160;

let isLeaving = false;

function prefersReducedMotion() {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function shouldHandleNavigation(event, link) {
  if (event.defaultPrevented || isLeaving) {
    return false;
  }

  if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
    return false;
  }

  if (!link || !link.href) {
    return false;
  }

  if (link.target && link.target !== '_self') {
    return false;
  }

  if (link.hasAttribute('download')) {
    return false;
  }

  const href = link.getAttribute('href');
  if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
    return false;
  }

  const nextUrl = new URL(link.href, window.location.href);
  const currentUrl = new URL(window.location.href);

  if (nextUrl.origin !== currentUrl.origin) {
    return false;
  }

  if (nextUrl.pathname === currentUrl.pathname && nextUrl.search === currentUrl.search && nextUrl.hash !== currentUrl.hash) {
    return false;
  }

  return true;
}

export function initPageTransitions() {
  document.addEventListener('click', (event) => {
    const link = event.target.closest('a');

    if (!shouldHandleNavigation(event, link)) {
      return;
    }

    if (prefersReducedMotion()) {
      return;
    }

    event.preventDefault();

    const targetUrl = new URL(link.href, window.location.href).toString();
    isLeaving = true;
    document.body.classList.add(LEAVE_ANIMATION_CLASS);

    window.setTimeout(() => {
      window.location.assign(targetUrl);
    }, LEAVE_DURATION_MS);
  });
}
