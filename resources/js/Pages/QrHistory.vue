<template>
    <AppLayout>
        <div class="qr-history">
            <h2>Історія QR-кодів</h2>

            <div v-if="flash?.success" class="flash success">
                {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="flash error">
                {{ flash.error }}
            </div>

            <div v-if="codes.length === 0" class="empty-history">
                Історія порожня. Згенеруйте та збережіть QR-коди, щоб вони з'явились тут.
            </div>

            <div v-else class="history-grid">
                <div v-for="item in codes" :key="item.id" class="history-card">
                    <div class="qr-preview">
                        <img :src="item.image_path" :alt="item.content">
                    </div>

                    <div class="card-content">
                        <p class="content-text">{{ truncateContent(item.content) }}</p>
                        <div class="card-meta">
                            <small>{{ formatDate(item.created_at) }}</small><br>
                            <small>Розмір: {{ item.size }}px</small>
                        </div>

                        <div v-if="item.is_dynamic" class="analytics">
                            <strong>Динамічний</strong>
                            <p>Перегляди: {{ item.scans_count ?? 0 }}</p>
                            <a :href="item.dynamic_url" target="_blank" class="visit-link">Перейти</a>
                        </div>

                        <div class="card-actions">
                            <button @click="downloadAgain(item)" class="action-btn download">Завантажити</button>
                            <button @click="copyToClipboard(item.content)" class="action-btn copy">Копіювати</button>
                            <button @click="deleteItem(item)" class="action-btn delete">Видалити</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import {usePage, router} from "@inertiajs/vue3"
import {ref, watch} from "vue"

const page = usePage()
const codes = ref(page.props.codes || [])
const flash = ref(page.props.flash || {})

// ✅ следим за обновлением flash-сообщений из Inertia
watch(() => page.props.flash, (val) => {
    flash.value = val
    if (val?.success) {
        // Можно заменить alert на toast при желании
        console.log('✅', val.success)
    }
})

// 🔹 Утилиты форматирования
const truncateContent = (text) => text.length > 50 ? text.substring(0, 50) + "..." : text
const formatDate = (d) => new Date(d).toLocaleString()

// 🔹 Скачать QR
const downloadAgain = (item) => {
    const link = document.createElement("a")
    link.href = item.image_path
    link.download = `qr-code-${new Date(item.created_at).getTime()}.png`
    link.click()
}

// 🔹 Копировать контент
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        alert("Текст скопійовано!")
    } catch {
        alert("Не вдалося скопіювати")
    }
}

// 🔹 Удаление с мгновенным обновлением списка
const deleteItem = (item) => {
    if (!confirm("Видалити цей QR-код?")) return

    router.delete(`/qr/${item.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            // сразу обновляем локально без перезагрузки
            codes.value = codes.value.filter(code => code.id !== item.id)
        },
    })
}
</script>

<style scoped>
.qr-history {
    max-width: 900px;
    margin: 2rem auto;
    background: #fff;
    border-radius: 8px;
    color: #2c3e50;
}

h2 {
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #34495e;
    text-align: center;
}

.flash {
    padding: 12px 16px;
    border-radius: 6px;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.flash.success {
    background-color: #e6ffed;
    color: #1a7f37;
    border: 1px solid #b3ffcc;
}

.flash.error {
    background-color: #ffe6e6;
    color: #b80000;
    border: 1px solid #ffb3b3;
}

.empty-history {
    text-align: center;
    font-style: italic;
    color: #666;
    padding: 3rem 0;
}

.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.history-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;
}

.history-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.qr-preview {
    background: #f5f5f5;
    padding: 1rem;
    text-align: center;
    border-radius: 6px 6px 0 0;
}

.qr-preview img {
    max-width: 100%;
    height: auto;
}

.card-content {
    padding: 1rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.content-text {
    font-size: 1rem;
    color: #34495e;
    margin-bottom: 0.5rem;
    word-break: break-word;
}

.card-meta small {
    color: #666;
    font-size: 0.85rem;
    line-height: 1.2;
}

.analytics {
    color: #2b5cff;
    margin: 1rem 0;
    font-weight: 600;
}

.visit-link {
    color: #2196f3;
    font-size: 0.9rem;
    text-decoration: underline;
}

.card-actions {
    display: flex;
    gap: 10px;
    margin-top: 1rem;
}

.action-btn {
    flex: 1;
    padding: 8px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    color: white;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.action-btn.download {
    background-color: #4caf50;
}

.action-btn.download:hover {
    background-color: #388e3c;
}

.action-btn.copy {
    background-color: #2196f3;
}

.action-btn.copy:hover {
    background-color: #0b7dda;
}

.action-btn.delete {
    background-color: #f44336;
}

.action-btn.delete:hover {
    background-color: #d32f2f;
}
</style>
