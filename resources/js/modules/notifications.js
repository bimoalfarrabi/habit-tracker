import { apiRequest } from '../utils/api';
import { query, queryAll } from '../utils/dom';

function renderPreview(notifications) {
    const container = query('[data-notification-preview-list]');

    if (!container) {
        return;
    }

    container.innerHTML = '';

    notifications.forEach((notification) => {
        const item = document.createElement('li');
        item.className = 'rounded-soft border border-borderCream bg-ivory p-3';
        item.innerHTML = `
            <p class="text-sm font-semibold text-ink">${notification.title}</p>
            <p class="mt-1 text-xs text-warmText">${notification.message}</p>
        `;

        container.appendChild(item);
    });

    if (notifications.length === 0) {
        container.innerHTML = '<li class="text-sm text-mutedText">Belum ada notifikasi.</li>';
    }
}

function updateBadge(unreadCount) {
    queryAll('[data-unread-badge]').forEach((badge) => {
        badge.textContent = String(unreadCount);
        badge.classList.toggle('hidden', unreadCount < 1);
    });
}

async function refreshNotifications() {
    try {
        const response = await apiRequest('/ajax/notifications');
        renderPreview(response.data.notifications ?? []);
        updateBadge(response.data.unread_count ?? 0);
    } catch {
        // Quiet fail to keep dashboard interactions stable.
    }
}

export function initNotifications() {
    if (!query('[data-notification-preview-list]') && queryAll('[data-unread-badge]').length === 0) {
        return;
    }

    refreshNotifications();
    window.setInterval(refreshNotifications, 30000);
}
