import { apiRequest } from '../utils/api';
import { on, query } from '../utils/dom';

function secondsBetween(startIso, endDate = new Date()) {
    return Math.max(0, Math.floor((endDate.getTime() - new Date(startIso).getTime()) / 1000));
}

function formatSeconds(seconds) {
    const hours = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const minutes = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const remainingSeconds = String(seconds % 60).padStart(2, '0');

    return `${hours}:${minutes}:${remainingSeconds}`;
}

export function initFocusTimer() {
    const page = query('[data-focus-page]');

    if (!page) {
        return;
    }

    const startForm = query('[data-focus-start-form]', page);
    const stopButton = query('[data-focus-stop]', page);
    const timerDisplay = query('[data-focus-elapsed]', page);
    const statusDisplay = query('[data-focus-status]', page);
    const focusedDisplay = query('[data-focus-focused]', page);
    const unfocusedDisplay = query('[data-focus-unfocused]', page);

    let currentSessionId = Number(page.dataset.runningSessionId || 0) || null;
    let startTime = page.dataset.runningStartTime || null;
    let focusedSeconds = Number(page.dataset.runningFocused || 0);
    let unfocusedSeconds = Number(page.dataset.runningUnfocused || 0);
    let interruptionCount = Number(page.dataset.runningInterruptions || 0);
    let isTabFocused = !document.hidden;

    function render() {
        const elapsed = startTime ? secondsBetween(startTime) : 0;
        if (timerDisplay) {
            timerDisplay.textContent = formatSeconds(elapsed);
        }

        if (focusedDisplay) {
            focusedDisplay.textContent = `${Math.floor(focusedSeconds / 60)} min`;
        }

        if (unfocusedDisplay) {
            unfocusedDisplay.textContent = `${Math.floor(unfocusedSeconds / 60)} min`;
        }

        if (statusDisplay) {
            statusDisplay.textContent = currentSessionId ? 'Running' : 'Idle';
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (!currentSessionId) {
            return;
        }

        if (document.hidden && isTabFocused) {
            isTabFocused = false;
            interruptionCount += 1;
            return;
        }

        if (!document.hidden && !isTabFocused) {
            isTabFocused = true;
        }
    });

    window.setInterval(() => {
        if (!currentSessionId || !startTime) {
            return;
        }

        if (isTabFocused) {
            focusedSeconds += 1;
        } else {
            unfocusedSeconds += 1;
        }

        render();
    }, 1000);

    on(startForm, 'submit', async (event) => {
        event.preventDefault();

        if (currentSessionId) {
            return;
        }

        const formData = new FormData(startForm);

        try {
            const response = await apiRequest('/ajax/focus-sessions/start', {
                method: 'POST',
                data: {
                    habit_id: formData.get('habit_id') || null,
                    planned_duration_minutes: formData.get('planned_duration_minutes') || null,
                    note: formData.get('note') || null,
                },
            });

            const session = response.data.session;
            currentSessionId = session.id;
            startTime = session.start_time;
            focusedSeconds = 0;
            unfocusedSeconds = 0;
            interruptionCount = 0;
            render();
        } catch (error) {
            window.alert(error?.payload?.message || 'Gagal memulai sesi fokus.');
        }
    });

    on(stopButton, 'click', async () => {
        if (!currentSessionId) {
            return;
        }

        try {
            await apiRequest(`/ajax/focus-sessions/${currentSessionId}/stop`, {
                method: 'POST',
                data: {
                    focused_duration_seconds: focusedSeconds,
                    unfocused_duration_seconds: unfocusedSeconds,
                    interruption_count: interruptionCount,
                    status: 'completed',
                },
            });

            currentSessionId = null;
            startTime = null;
            render();
            window.location.reload();
        } catch (error) {
            window.alert(error?.payload?.message || 'Gagal menghentikan sesi fokus.');
        }
    });

    render();
}
