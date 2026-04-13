import './bootstrap';
import { initFocusTimer } from './modules/focus-timer';
import { initNotifications } from './modules/notifications';
import { initPageTransitions } from './modules/page-transitions';
import { initQuickCheckin } from './modules/quick-checkin';
import { initSmoothScroll } from './modules/smooth-scroll';
import { initVerificationCooldown } from './modules/verification-cooldown';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

initPageTransitions();
initSmoothScroll();
initQuickCheckin();
initNotifications();
initFocusTimer();
initVerificationCooldown();
