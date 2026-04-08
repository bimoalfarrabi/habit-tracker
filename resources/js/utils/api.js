function csrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');
}

export async function apiRequest(url, { method = 'GET', data = null, headers = {} } = {}) {
    const requestHeaders = {
        Accept: 'application/json',
        ...headers,
    };

    if (method !== 'GET' && method !== 'HEAD') {
        requestHeaders['X-CSRF-TOKEN'] = csrfToken() ?? '';
        requestHeaders['Content-Type'] = 'application/json';
    }

    const response = await fetch(url, {
        method,
        headers: requestHeaders,
        body: data ? JSON.stringify(data) : null,
        credentials: 'same-origin',
    });

    const isJson = response.headers.get('content-type')?.includes('application/json');
    const payload = isJson ? await response.json() : null;

    if (!response.ok) {
        throw {
            status: response.status,
            payload,
        };
    }

    return payload;
}
