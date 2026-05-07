import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

/**
 * CSRF Token — read from meta tag or inline variable, set as axios default.
 */
const csrfMeta = document.head.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfMeta?.content || window.__csrfToken || '';
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

/**
 * 419 CSRF auto-recovery: if a request fails with 419, refresh the page
 * to get a new token, then let the user retry.
 */
window.axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 419 && !error.config._csrf_retried) {
            error.config._csrf_retried = true;
            try {
                // Fetch current page to get a fresh session + CSRF token
                const html = await fetch(window.location.href, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'text/html' },
                }).then(r => r.text());

                const m = html.match(/name="csrf-token"\s+content="([^"]+)"/);
                if (m) {
                    const fresh = m[1];
                    if (csrfMeta) csrfMeta.content = fresh;
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = fresh;
                    error.config.headers['X-CSRF-TOKEN'] = fresh;
                    return axios(error.config);
                }
            } catch (e) {
                // refresh failed, fall through to reject
            }
        }
        return Promise.reject(error);
    }
);
