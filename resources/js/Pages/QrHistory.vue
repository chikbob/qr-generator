<template>
    <AppLayout>
        <div class="qr-history">
            <h2>Історія QR-кодів</h2>

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
import { usePage, router } from "@inertiajs/vue3"
import { ref, watch } from "vue"

const page = usePage()
const codes = ref(page.props.codes || [])

watch(() => page.props.flash?.success, val => val && alert(val))

const truncateContent = (text) => text.length > 50 ? text.substring(0, 50) + "..." : text
const formatDate = (d) => new Date(d).toLocaleString()

const downloadAgain = (item) => {
    const link = document.createElement("a")
    link.href = item.image_path
    link.download = `qr-code-${new Date(item.created_at).getTime()}.png`
    link.click()
}

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        alert("Текст скопійовано!")
    } catch {
        alert("Не вдалося скопіювати")
    }
}

const deleteItem = (item) => {
    if (!confirm("Видалити цей QR-код?")) return
    router.delete(`/qr/${item.id}`)
}
</script>

<style scoped>
.analytics {
    margin: 10px 0;
    color: #2b5cff;
}

.visit-link {
    color: #2196f3;
    text-decoration: underline;
    font-size: 0.9em;
}

.qr-history {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.empty-history {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.history-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: 0.2s;
}

.history-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.qr-preview {
    background: #f5f5f5;
    padding: 15px;
    text-align: center;
}

.qr-preview img {
    max-width: 100%;
    height: auto;
}

.card-content {
    padding: 15px;
}

.card-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    flex: 1;
    padding: 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
}

.action-btn.download {
    background-color: #4caf50;
}

.action-btn.copy {
    background-color: #2196f3;
}

.action-btn.delete {
    background-color: #f44336;
}
</style>
