export const query = (selector, root = document) => root.querySelector(selector);
export const queryAll = (selector, root = document) => Array.from(root.querySelectorAll(selector));

export function on(target, eventName, handler) {
    if (!target) {
        return;
    }

    target.addEventListener(eventName, handler);
}
