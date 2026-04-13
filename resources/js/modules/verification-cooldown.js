import { queryAll } from '../utils/dom';

function formatLabel(template, seconds) {
    return template.replace(':seconds', String(seconds));
}

export function initVerificationCooldown() {
    const resendButtons = queryAll('[data-verification-resend-button]');

    if (resendButtons.length === 0) {
        return;
    }

    let remainingSeconds = Math.max(
        ...resendButtons.map((button) => Number.parseInt(button.dataset.cooldownSeconds ?? '0', 10) || 0),
        0,
    );

    const cooldownMessages = queryAll('[data-verification-cooldown-message]');
    const cooldownSecondLabels = queryAll('[data-verification-cooldown-seconds]');

    const applyState = () => {
        const isCoolingDown = remainingSeconds > 0;

        resendButtons.forEach((button) => {
            const defaultLabel = button.dataset.defaultLabel ?? button.textContent?.trim() ?? '';
            const countdownLabel = button.dataset.countdownLabel ?? 'Tunggu :seconds detik';

            button.disabled = isCoolingDown;
            button.textContent = isCoolingDown
                ? formatLabel(countdownLabel, remainingSeconds)
                : defaultLabel;
        });

        cooldownMessages.forEach((message) => {
            message.classList.toggle('hidden', !isCoolingDown);
        });

        cooldownSecondLabels.forEach((label) => {
            label.textContent = String(remainingSeconds);
        });
    };

    applyState();

    if (remainingSeconds < 1) {
        return;
    }

    const intervalId = window.setInterval(() => {
        remainingSeconds = Math.max(0, remainingSeconds - 1);
        applyState();

        if (remainingSeconds < 1) {
            window.clearInterval(intervalId);
        }
    }, 1000);
}
