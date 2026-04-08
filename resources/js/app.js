import './bootstrap';
import { initFocusTimer } from './modules/focus-timer';
import { initNotifications } from './modules/notifications';
import { initPageTransitions } from './modules/page-transitions';
import { initQuickCheckin } from './modules/quick-checkin';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

initPageTransitions();
initQuickCheckin();
initNotifications();
initFocusTimer();
