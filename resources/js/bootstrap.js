// resources/js/bootstrap.js
import axios from 'axios';

// всегда указываем, что наши запросы будут отправляться как XHR
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// используем куки (XSRF-TOKEN) при запросах same-site
axios.defaults.withCredentials = true;

// helper — чекаем meta csrf и cookie XSRF-TOKEN и кладём в axios headers
function setCsrfHeadersFromDom() {
    // meta csrf-token
    const meta = document.head.querySelector('meta[name="csrf-token"]');
    if (meta && meta.content) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = meta.content;
    }

    // cookie XSRF-TOKEN (Laravel устанавливает его) -> ставим в X-XSRF-TOKEN (axios сам может поставить, но явимая установка безопаснее)
    const match = document.cookie.match(/(^|;)\s*XSRF-TOKEN=([^;]+)/);
    if (match && match.length > 2) {
        try {
            axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(match[2]);
        } catch (e) {
            // ignore decode errors
        }
    }
}

// задать первичные заголовки при старте
setCsrfHeadersFromDom();

// обновляем заголовки после navigations / finish (Inertia меняет head и cookie)
document.addEventListener('inertia:navigate', setCsrfHeadersFromDom);
document.addEventListener('inertia:finish', setCsrfHeadersFromDom);

// ещё: перехватчик запросов гарантирует, что перед каждым запросом будет актуальный header
axios.interceptors.request.use(function (config) {
    // обновляем на момент запроса (на всякий случай)
    setCsrfHeadersFromDom();
    return config;
}, function (error) {
    return Promise.reject(error);
});

window.axios = axios;
