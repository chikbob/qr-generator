<template>
    <AppLayout>
        <div class="qr-history">
            <h2>{{ t('qrHistory.title') }}</h2>

            <!-- 🔍 Панель управления -->
            <div class="controls">
                <input
                    type="text"
                    v-model="searchQuery"
                    :placeholder="t('qrHistory.searchPlaceholder')"
                    class="search-input"
                    style="width: 300px"
                />

                <select v-model="filterType" class="filter-select">
                    <option value="all">{{ t('qrHistory.filter.all') }}</option>
                    <option value="dynamic">{{ t('qrHistory.filter.dynamic') }}</option>
                    <option value="static">{{ t('qrHistory.filter.static') }}</option>
                </select>

                <select v-model="sortOrder" class="sort-select" style="width: 200px">
                    <option value="desc">{{ t('qrHistory.sort.desc') }}</option>
                    <option value="asc">{{ t('qrHistory.sort.asc') }}</option>
                </select>

                <!--
                <button @click="showDeleteAllModal = true" class="delete-all-btn">
                    🗑️ {{ t('qrHistory.deleteAll') }}
                </button>
                -->
            </div>

            <!-- Flash сообщения -->
            <div name="fade">
                <div v-if="flash?.success" class="flash success">{{ tMaybe(flash.success) }}</div>
                <div v-if="flash?.error" class="flash error">{{ tMaybe(flash.error) }}</div>
            </div>

            <!-- Пустая история -->
            <div v-if="codes.length === 0" class="empty-history">
                <p>{{ t('qrHistory.empty.title') }}</p>
                <small>{{ t('qrHistory.empty.subtitle') }}</small>
            </div>

            <!-- Список кодов -->
            <div v-else class="history-grid">
                <div v-for="item in codes" :key="item.id" class="history-card">
                    <div class="qr-header">
                        <div class="qr-preview">
                            <img :src="getPreviewSrc(item)" :alt="item.content"/>
                        </div>
                        <div class="qr-meta">
                            <p class="content-text" :title="item.content">
                                {{ formatQrDescription(item) }}
                            </p>
                            <div class="meta-row">
                                <span class="meta-item">📅 {{ formatDate(item.created_at) }}</span>
                                <span class="meta-item">📏 {{ item.size }} px</span>
                            </div>
                        </div>
                    </div>

                    <div class="qr-body">
                        <div class="qr-type" :class="{ dynamic: item.is_dynamic }">
                            {{ item.is_dynamic ? t('qrHistory.type.dynamic') : t('qrHistory.type.static') }}
                        </div>

                        <div v-if="item.is_dynamic" class="qr-stats">
                            <span class="stat"><strong>📈 {{
                                    t('qrHistory.stats.views')
                                }}:</strong> {{ item.scans_count ?? 0 }}</span>
                            <a :href="resolveDynamicUrl(item, true)" target="_blank" rel="noopener noreferrer" class="visit-link">🔗
                                {{ t('qrHistory.stats.visit') }}</a>
                        </div>

                        <div class="card-actions mid-actions">
                            <button @click="downloadFile(item, 'png')" class="action-btn download">🖼️ PNG</button>
                            <button @click="downloadFile(item, 'svg')" class="action-btn download-alt">🧩 SVG</button>
                        </div>

                        <div class="card-actions bottom-actions">
                            <button @click="copyToClipboard(item.content)" class="action-btn copy">📋
                                {{ t('qrHistory.actions.copy') }}
                            </button>
                            <button
                                v-if="item.is_dynamic"
                                @click="openAnalytics(item.id)"
                                class="action-btn analytics-btn"
                            >
                                📊 {{ t('qrHistory.actions.analytics') }}
                            </button>
                            <button @click="deleteItem(item)" class="action-btn delete">🗑️
                                {{ t('qrHistory.actions.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="pagination.last_page > 1" class="pagination">
                <button
                    class="page-btn"
                    :disabled="pagination.current_page <= 1"
                    @click="goToPage(pagination.current_page - 1)"
                >
                    ← Prev
                </button>

                <span class="page-meta">
                    {{ pagination.current_page }} / {{ pagination.last_page }}
                    ({{ pagination.total }})
                </span>

                <button
                    class="page-btn"
                    :disabled="pagination.current_page >= pagination.last_page"
                    @click="goToPage(pagination.current_page + 1)"
                >
                    Next →
                </button>
            </div>

            <!-- Модальное окно для удаления всех QR-кодов (закомментировано) -->
            <!--
            <div
                v-if="showDeleteAllModal"
                class="modal-overlay"
                @click.self="showDeleteAllModal = false"
            >
                <div class="modal-content">
                    <h3>{{ t('qrHistory.deleteModal.title') }}</h3>
                    <p>{{ t('qrHistory.deleteModal.message') }}</p>
                    <div class="modal-actions">
                        <button @click="deleteAll" class="action-btn delete">{{ t('qrHistory.deleteModal.confirm') }}</button>
                        <button @click="showDeleteAllModal = false" class="action-btn cancel">{{ t('qrHistory.deleteModal.cancel') }}</button>
                    </div>
                </div>
            </div>
            -->
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import {usePage, router} from "@inertiajs/vue3"
import {ref, watch} from "vue"
import {useI18n} from '@/Lang/useI18n'
import {formatDateTimeUtcPlus3} from '@/utils/datetime'

const {t, tMaybe} = useI18n()

const page = usePage()
const codes = ref(page.props.codes || [])
const flash = ref(page.props.flash || {})
const pagination = ref(page.props.pagination || {current_page: 1, last_page: 1, total: 0})
const initialFilters = page.props.filters || {}

const searchQuery = ref(initialFilters.search || "")
const filterType = ref(initialFilters.filter || "all")
const sortOrder = ref(initialFilters.sort || "desc")

const showDeleteAllModal = ref(false)
let filtersDebounceTimer = null

watch(() => page.props.flash, (val) => (flash.value = val))
watch(() => page.props.codes, (val) => {
    codes.value = val || []
})
watch(() => page.props.pagination, (val) => {
    pagination.value = val || {current_page: 1, last_page: 1, total: 0}
})
watch(() => page.props.filters, (val) => {
    if (!val) return
    searchQuery.value = val.search || ""
    filterType.value = val.filter || "all"
    sortOrder.value = val.sort || "desc"
})

const formatQrDescription = (item) => {
    const content = item.content || ''
    const type = item.type || 'text'

    try {
        switch (type) {
            case 'email': {
                // mailto:someone@example.com?subject=Hi
                // Но иногда someone@example.com тоже закодирован в процентах — декодируем
                const url = new URL(content)
                if (url.protocol === 'mailto:') {
                    const toRaw = url.pathname
                    const to = decodeURIComponent(toRaw) // декодируем email
                    const subjectRaw = url.searchParams.get('subject')
                    const subject = subjectRaw ? decodeURIComponent(subjectRaw) : null
                    return subject
                        ? `Email to ${to} (subject: ${subject})`
                        : `Email to ${to}`
                }
                break
            }
            case 'phone': {
                if (content.startsWith('tel:')) {
                    return `Phone: ${decodeURIComponent(content.slice(4))}`
                }
                break
            }
            case 'sms': {
                const url = new URL(content)
                if (url.protocol === 'sms:') {
                    const number = decodeURIComponent(url.pathname)
                    const bodyRaw = url.searchParams.get('body')
                    const body = bodyRaw ? decodeURIComponent(bodyRaw) : null
                    return body
                        ? `SMS to ${number} (message: ${body})`
                        : `SMS to ${number}`
                }
                break
            }
            case 'wifi': {
                const ssidMatch = content.match(/S:([^;]+);/)
                const encryptionMatch = content.match(/T:([^;]+);/)
                const ssid = ssidMatch ? decodeURIComponent(ssidMatch[1]) : ''
                const encryption = encryptionMatch ? decodeURIComponent(encryptionMatch[1]) : ''
                return `WiFi: ${ssid} (${encryption})`
            }
            case 'contact': {
                const fnMatch = content.match(/FN:(.+)/)
                return fnMatch ? `Contact: ${decodeURIComponent(fnMatch[1])}` : 'Contact info'
            }
            case 'location': {
                if (content.includes('google.com')) return 'Location (Google Maps)'
                if (content.includes('yandex.ru')) return 'Location (Yandex Maps)'
                return 'Location'
            }
            case 'pdf': {
                return 'PDF document'
            }
            default: {
                return content.length > 50 ? content.slice(0, 50) + '...' : content
            }
        }
    } catch (e) {
        return content.length > 50 ? content.slice(0, 50) + '...' : content
    }
}

const truncateContent = (text) => (text.length > 50 ? text.substring(0, 50) + "..." : text)
const formatDate = (d) => formatDateTimeUtcPlus3(d)
const openAnalytics = (id) => router.visit(`/qr/${id}/analytics`)

const resolveDynamicUrl = (item, absolute = false) => {
    const relativeOrFull = item.dynamic_url_full || item.dynamic_url || item.content

    if (!absolute) {
        return relativeOrFull
    }

    if (!relativeOrFull) {
        return ''
    }

    if (/^https?:\/\//i.test(relativeOrFull)) {
        return relativeOrFull
    }

    if (typeof window !== 'undefined' && window.location?.origin) {
        const path = relativeOrFull.startsWith('/') ? relativeOrFull : `/${relativeOrFull}`
        return `${window.location.origin}${path}`
    }

    return item.dynamic_url_full || relativeOrFull
}

const getPreviewSrc = (item) => item?.image_path || ''

const buildQueryParams = (pageNum = 1) => ({
    page: pageNum,
    search: searchQuery.value.trim() || undefined,
    filter: filterType.value !== 'all' ? filterType.value : undefined,
    sort: sortOrder.value !== 'desc' ? sortOrder.value : undefined,
})

const fetchHistory = (pageNum = 1) => {
    router.get('/history', buildQueryParams(pageNum), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['codes', 'pagination', 'filters', 'flash'],
    })
}

const goToPage = (pageNum) => {
    if (pageNum < 1 || pageNum > (pagination.value.last_page || 1)) return
    fetchHistory(pageNum)
}

watch([searchQuery, filterType, sortOrder], () => {
    if (filtersDebounceTimer) {
        clearTimeout(filtersDebounceTimer)
    }

    filtersDebounceTimer = setTimeout(() => {
        fetchHistory(1)
    }, 250)
})

const downloadFile = async (item, type = "png") => {
    try {
        const {default: QRCode} = await import("qrcode")

        const qrContent = item.is_dynamic
            ? resolveDynamicUrl(item, true)
            : item.content

        if (type === "png") {
            const canvas = document.createElement("canvas")
            await QRCode.toCanvas(canvas, qrContent, {
                margin: 4,
                scale: 8,
                color: {dark: item.color_dark || "#000000", light: item.color_light || "#ffffff"},
                width: item.size || 200,
            })
            const link = document.createElement("a")
            link.href = canvas.toDataURL("image/png")
            link.download = `qr-code-${item.id}.png`
            link.click()
            return
        }

        if (type === "svg") {
            const svg = await QRCode.toString(qrContent, {
                type: "svg",
                margin: 4,
                color: {dark: item.color_dark || "#000000", light: item.color_light || "#ffffff"},
                width: item.size || 200,
            })
            const blob = new Blob([svg], {type: "image/svg+xml"})
            const url = URL.createObjectURL(blob)
            const link = document.createElement("a")
            link.href = url
            link.download = `qr-code-${item.id}.svg`
            link.click()
            URL.revokeObjectURL(url)
        }
    } catch (err) {
        console.error(t('qrHistory.errors.downloadError'), err)
        alert(t('qrHistory.errors.downloadFailed'))
    }
}

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        alert(t('qrHistory.actions.copySuccess'))
    } catch {
        alert(t('qrHistory.actions.copyFail'))
    }
}

const deleteItem = (item) => {
    if (!confirm(t('qrHistory.confirm.deleteItem'))) return
    router.delete(`/qr/${item.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            fetchHistory(pagination.value.current_page || 1)
        },
    })
}

// const deleteAll = () => {
//     if (!confirm(t('qrHistory.confirm.deleteAll'))) return
//
//     router.delete('/qr/delete-all', {
//         preserveScroll: true,
//         onSuccess: () => {
//             codes.value = []
//             showDeleteAllModal.value = false
//             alert(t('qrHistory.confirm.deleteAllSuccess'))
//         },
//         onError: () => {
//             alert(t('qrHistory.confirm.deleteAllFail'))
//         },
//     })
// }
</script>

<style lang="scss" scoped>
$color-primary: #e095bc; // основной розовый
$color-primary-dark: #bd6592; // тёмный акцент
$color-primary-light: #fce7f3; // мягкий фон

$color-secondary: #8b5cf6; // НЕ трогаем (используется в hover ссылок)
$color-danger: #ef4444; // НЕ трогаем
$color-danger-dark: #b91c1c; // НЕ трогаем

$color-bg: #ffffff; // карточки остаются белыми
$color-text: #0f172a; // как в Scanner
$color-text-light: #bd6592; // акцентный текст
$font-main: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

.qr-history {
    max-width: 1100px; /* было 1000px */
    margin: 3rem auto 5rem;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    background: #fff;
    box-shadow: 0 16px 48px rgba(236, 72, 153, 0.15);
    color: #34495e;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
    font-weight: 900;
    font-size: 2.4rem; /* было 2.25rem */
    margin-bottom: 2rem;
    text-align: center;
    background: linear-gradient(135deg, $color-primary, $color-primary-dark);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    user-select: none;
}

.controls {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.25rem;
    margin-bottom: 2.5rem;
    align-items: center;
}

.search-input,
.filter-select,
.sort-select {
    padding: 0.85rem 1.2rem;
    border: 2px solid $color-primary-light;
    border-radius: 16px;
    font-size: 1.1rem;
    font-weight: 600;
    color: $color-primary-dark;
    background: $color-primary-light;
    box-shadow: inset 0 4px 6px rgba($color-primary, 0.1);
    transition: border-color 0.3s ease,
    box-shadow 0.3s ease,
    background-color 0.3s ease;

    &:focus {
        outline: none;
        border-color: $color-primary;
        box-shadow: 0 0 12px 3px rgba($color-primary, 0.5);
        background-color: #fff0f6;
    }

    &::placeholder {
        color: darken($color-primary-light, 15%);
        font-style: italic;
    }
}

.flash {
    padding: 16px 24px;
    border-radius: 20px;
    font-weight: 700;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    text-align: center;
    user-select: none;
    box-shadow: 0 0 10px transparent;
    transition: box-shadow 0.3s ease;

    &.success {
        background-color: #daf8e3;
        color: #166534;
        box-shadow: 0 0 15px #34d399aa;
    }

    &.error {
        background-color: #fee2e2;
        color: $color-danger-dark;
        box-shadow: 0 0 15px #f8717188;
    }
}

.empty-history {
    text-align: center;
    padding: 6rem 1rem;
    color: $color-primary-dark;
    font-style: italic;
    font-weight: 700;
    font-size: 1.3rem;
}

.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.pagination {
    margin-top: 2.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.page-btn {
    border: none;
    border-radius: 12px;
    padding: 0.65rem 1rem;
    font-weight: 700;
    background: linear-gradient(135deg, $color-primary, $color-primary-dark);
    color: #fff;
    cursor: pointer;
    transition: opacity 0.2s ease, transform 0.2s ease;

    &:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    &:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
}

.page-meta {
    font-weight: 700;
    color: $color-primary-dark;
}

.history-card {
    background: $color-bg;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 6px 6px 15px rgba(219, 39, 119, 0.15),
    -6px -6px 20px rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
    box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
        transform: translateY(-8px);
        box-shadow: 10px 10px 30px rgba(219, 39, 119, 0.3),
        -10px -10px 30px rgba(255, 255, 255, 0.9);
    }
}

.qr-header {
    display: flex;
    align-items: center;
    gap: 1.6rem;
    padding: 1.8rem 2rem 1.2rem;
    border-bottom: 2px solid lighten($color-primary-light, 10%);
}

.qr-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
}

.qr-preview img {
    width: 110px;
    height: 110px;
    object-fit: contain;
    border-radius: 20px;
    background: #fff;
    padding: 10px;
    box-shadow: 0 0 15px 3px rgba($color-primary, 0.3),
    inset 0 0 8px rgba(255, 255, 255, 0.6);
    transition: transform 0.3s ease;

    &:hover {
        transform: scale(1.05);
    }
}

.content-text {
    font-weight: 700;
    color: $color-text-light;
    margin-bottom: 0.6rem;
    word-break: break-word;
    font-size: 1.2rem;
    max-width: 240px;
    user-select: text;
}

.meta-row {
    display: flex;
    gap: 1.2rem;
    flex-wrap: wrap;
    font-weight: 600;
    color: $color-primary-dark;
    font-size: 0.9rem;
}

.qr-body {
    padding: 1.4rem 2rem 2rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 200px;
}

.qr-type {
    font-weight: 700;
    padding: 0.6rem 1.4rem;
    border-radius: 20px;
    display: inline-block;
    margin-bottom: 1.3rem;
    background: $color-primary-light;
    color: $color-text-light;
    user-select: none;
    text-align: center;
    font-size: 1.1rem;
    letter-spacing: 0.05em;
    box-shadow: inset 0 2px 6px rgba($color-primary, 0.3);

    &.dynamic {
        background: linear-gradient(90deg, $color-primary, #ec4899);
        color: white;
        box-shadow: 0 0 14px 3px rgba(#ec4899, 0.8);
    }
}

.qr-stats {
    margin-bottom: 1.3rem;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    color: darken($color-primary-dark, 10%);
    user-select: none;
}

.visit-link {
    color: $color-primary;
    font-weight: 700;
    text-decoration: none;
    transition: color 0.4s ease, text-shadow 0.4s ease;
    user-select: text;

    &:hover {
        text-decoration: underline;
        color: $color-secondary;
        text-shadow: 0 0 8px rgba($color-secondary, 0.8);
    }
}

.card-actions {
    display: flex;
    gap: 14px;
}

.mid-actions {
    justify-content: space-between;
    margin-top: 0.7rem;
}

.bottom-actions {
    justify-content: space-between;
    margin-top: 1.3rem;
}

.action-btn {
    flex: 1;
    padding: 0.7rem 1.1rem;
    border: none;
    border-radius: 16px;
    color: white;
    font-weight: 700;
    font-size: 1.05rem;
    cursor: pointer;
    transition: background-color 0.35s ease,
    box-shadow 0.35s ease,
    transform 0.2s ease;
    user-select: none;
    box-shadow: 0 6px 25px rgba($color-primary, 0.3);

    &:hover {
        transform: translateY(-3px);
    }

    &.download {
        background: linear-gradient(45deg, #ec4899, #db2777);
        box-shadow: 0 0 20px #ec4899cc;

        &:hover {
            background: linear-gradient(45deg, #db2777, #ec4899);
            box-shadow: 0 0 30px #db2777cc;
        }
    }

    &.download-alt {
        background: linear-gradient(45deg, #d946ef, #9333ea);
        box-shadow: 0 0 20px #9333eaaa;

        &:hover {
            background: linear-gradient(45deg, #9333ea, #d946ef);
            box-shadow: 0 0 30px #d946efcc;
        }
    }

    &.copy {
        background: linear-gradient(45deg, #8b5cf6, #7c3aed);
        box-shadow: 0 0 20px #7c3aedcc;

        &:hover {
            background: linear-gradient(45deg, #7c3aed, #8b5cf6);
            box-shadow: 0 0 30px #8b5cf6cc;
        }
    }

    &.analytics-btn {
        background: linear-gradient(45deg, #6366f1, #4f46e5);
        box-shadow: 0 0 20px #4f46e5cc;

        &:hover {
            background: linear-gradient(45deg, #4f46e5, #6366f1);
            box-shadow: 0 0 30px #6366f1cc;
        }
    }

    &.delete {
        background: linear-gradient(45deg, #ef4444, #b91c1c);
        box-shadow: 0 0 20px #b91c1ccc;

        &:hover {
            background: linear-gradient(45deg, #b91c1c, #ef4444);
            box-shadow: 0 0 30px #ef4444cc;
        }
    }
}

/* Анимация появления flash */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.4s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Мобильная адаптация */
@media (max-width: 700px) {
    .history-grid {
        grid-template-columns: 1fr;
    }

    .qr-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .content-text {
        max-width: 100%;
    }

    .qr-body {
        min-height: auto;
    }

    .controls {
        justify-content: center;
    }

    .search-input,
    .filter-select,
    .sort-select {
        min-width: 140px;
        font-size: 1rem;
    }

    .action-btn {
        font-size: 0.95rem;
        padding: 0.5rem 0.8rem;
    }
}
</style>
