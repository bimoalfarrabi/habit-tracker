import { apiRequest } from '../utils/api';
import { on, queryAll } from '../utils/dom';

async function submitQuickCheckin(button) {
    const habitId = Number(button.dataset.habitId);

    if (!habitId) {
        return;
    }

    const status = button.dataset.status || 'completed';
    const qty = Number(button.dataset.qty || 1);

    button.disabled = true;

    try {
        const response = await apiRequest('/ajax/habit-logs/quick-checkin', {
            method: 'POST',
            data: {
                habit_id: habitId,
                status,
                qty,
            },
        });

        const statusLabel = document.querySelector(`[data-habit-status="${habitId}"]`);

        if (statusLabel) {
            statusLabel.textContent = response.data.action === 'created'
                ? 'Checked in'
                : 'Updated';
        }

        button.textContent = 'Checked';
    } catch (error) {
        window.alert(error?.payload?.message || 'Gagal melakukan check-in cepat.');
    } finally {
        button.disabled = false;
    }
}

export function initQuickCheckin() {
    queryAll('[data-quick-checkin]').forEach((button) => {
        on(button, 'click', () => submitQuickCheckin(button));
    });
}
