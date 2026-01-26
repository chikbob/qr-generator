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
                />

                <select v-model="filterType" class="filter-select">
                    <option value="all">{{ t('qrHistory.filter.all') }}</option>
                    <option value="dynamic">{{ t('qrHistory.filter.dynamic') }}</option>
                    <option value="static">{{ t('qrHistory.filter.static') }}</option>
                </select>

                <select v-model="sortOrder" class="sort-select">
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
                <div v-if="flash?.success" class="flash success">{{ flash.success }}</div>
                <div v-if="flash?.error" class="flash error">{{ flash.error }}</div>
            </div>

            <!-- Пустая история -->
            <div v-if="filteredCodes.length === 0" class="empty-history">
                <p>{{ t('qrHistory.empty.title') }}</p>
                <small>{{ t('qrHistory.empty.subtitle') }}</small>
            </div>

            <!-- Список кодов -->
            <div v-else class="history-grid">
                <div v-for="item in filteredCodes" :key="item.id" class="history-card">
                    <div class="qr-header">
                        <div class="qr-preview">
                            <img :src="item.image_path" :alt="item.content"/>
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
                            <a :href="item.dynamic_url" target="_blank" class="visit-link">🔗
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
import {ref, computed, watch} from "vue"
import {useI18n} from '@/lang/useI18n'

const {t} = useI18n()

const page = usePage()
const codes = ref(page.props.codes || [])
const flash = ref(page.props.flash || {})

const searchQuery = ref("")
const filterType = ref("all")
const sortOrder = ref("desc")

const showDeleteAllModal = ref(false)

watch(() => page.props.flash, (val) => (flash.value = val))

const filteredCodes = computed(() => {
    let list = [...codes.value]

    if (filterType.value === "dynamic") {
        list = list.filter((c) => c.is_dynamic)
    } else if (filterType.value === "static") {
        list = list.filter((c) => !c.is_dynamic)
    }

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase()
        list = list.filter((c) => c.content.toLowerCase().includes(q))
    }

    list.sort((a, b) => {
        return sortOrder.value === "asc"
            ? new Date(a.created_at) - new Date(b.created_at)
            : new Date(b.created_at) - new Date(a.created_at)
    })

    return list
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
const formatDate = (d) => new Date(d).toLocaleString()
const openAnalytics = (id) => router.visit(`/qr/${id}/analytics`)

const downloadFile = async (item, type = "png") => {
    try {
        const {default: QRCode} = await import("qrcode")

        if (type === "png") {
            const canvas = document.createElement("canvas")
            await QRCode.toCanvas(canvas, item.content, {
                margin: 4,
                scale: 8,
                color: {dark: "#000000", light: "#ffffff"},
                width: item.size || 200,
            })
            const link = document.createElement("a")
            link.href = canvas.toDataURL("image/png")
            link.download = `qr-code-${item.id}.png`
            link.click()
            return
        }

        if (type === "svg") {
            const svg = await QRCode.toString(item.content, {
                type: "svg",
                margin: 4,
                color: {dark: "#000000", light: "#ffffff"},
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
            codes.value = codes.value.filter((code) => code.id !== item.id)
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

<style scoped>
/* стили без изменений */
.qr-history {
    max-width: 1000px;
    margin: auto;
    border-radius: 16px;
    padding: 2rem;
    color: #34495e;
}

h2 {
    font-weight: 700;
    font-size: 1.9rem;
    margin: 0 0 1.5rem;
    text-align: center;
    color: #34495e;
}

/* 🔍 Панель управления */
.controls {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.search-input,
.filter-select,
.sort-select {
    padding: 0.6rem 0.9rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: 0.2s;
}

.search-input {
    flex: 1 1 250px;
    min-width: 200px;
}

.search-input:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

.filter-select,
.sort-select {
    flex: 0 0 170px;
}

.delete-all-btn {
    flex: 0 0 auto;
    background-color: #ef4444;
    color: white;
    border: none;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.25s ease;
    white-space: nowrap;
}

.delete-all-btn:hover {
    background-color: #dc2626;
}

/* Flash */
.flash {
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 1.2rem;
    text-align: center;
}

.flash.success {
    background-color: #e8fce8;
    color: #166534;
}

.flash.error {
    background-color: #fee2e2;
    color: #b91c1c;
}

/* Сетка */
.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    gap: 1.5rem;
}

.history-card {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    transition: 0.3s ease;
}

.history-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
}

/* Заголовок */
.qr-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.2rem 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.qr-preview img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 8px;
    background: #f3f4f6;
    padding: 6px;
}

.content-text {
    font-weight: 600;
    color: #1e3a8a;
    margin-bottom: 0.4rem;
    word-break: break-word;
    text-align: left;
}

.meta-row {
    display: flex;
    gap: 0.8rem;
    flex-wrap: wrap;
}

.meta-item {
    font-size: 0.85rem;
    color: #6b7280;
}

/* Тело карточки */
.qr-body {
    padding: 1rem 1.2rem 1.2rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 200px;
}

.qr-type {
    font-weight: 600;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 0.6rem;
    background: #e5e7eb;
    color: #374151;
}

.qr-type.dynamic {
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    color: white;
}

/* Аналитика */
.qr-stats {
    margin-bottom: 0.6rem;
    font-size: 0.95rem;
    display: flex;
    justify-content: space-between;
}

.visit-link {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
}

.visit-link:hover {
    text-decoration: underline;
}

/* Кнопки */
.card-actions {
    display: flex;
    gap: 8px;
}

.mid-actions {
    justify-content: space-between;
    margin-top: 0.4rem;
}

.bottom-actions {
    justify-content: space-between;
    margin-top: 0.8rem;
}

.action-btn {
    flex: 1;
    padding: 0.6rem 0.8rem;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: 0.25s ease;
}

.download {
    background: #10b981;
}

.download:hover {
    background: #059669;
}

.download-alt {
    background: #0ea5e9;
}

.download-alt:hover {
    background: #0284c7;
}

.copy {
    background: #3b82f6;
}

.copy:hover {
    background: #2563eb;
}

.analytics-btn {
    background: #8b5cf6;
}

.analytics-btn:hover {
    background: #7c3aed;
}

.delete {
    background: #ef4444;
}

.delete:hover {
    background: #dc2626;
}

/* Пустая история */
.empty-history {
    text-align: center;
    padding: 3rem 0;
    color: #6b7280;
    font-style: italic;
}

/* Модальное окно */
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-content h3 {
    margin-bottom: 1rem;
    font-weight: 700;
}

.modal-content p {
    margin-bottom: 1.5rem;
    color: #444;
}

.modal-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.action-btn.cancel {
    background: #999;
}

.action-btn.cancel:hover {
    background: #777;
}
</style>
