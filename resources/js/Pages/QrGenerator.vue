<template>
    <AppLayout>
        <div class="qr-generator">
            <h1>Генератор QR-кодів</h1>

            <div class="input-container">
                <textarea
                    v-model="inputText"
                    @input="handleInput"
                    placeholder="Введіть текст або посилання для генерації QR-коду"
                    rows="4"
                    :class="{ 'error': showWarning }"
                ></textarea>
                <div v-if="showWarning" class="warning-message">
                    Увага: Занадто довгий текст може знизити читаність QR-коду
                </div>
                <div class="char-count">
                    Символів: {{ inputText.length }} / 500
                </div>
            </div>

            <div class="qr-container" v-if="inputText">
                <div class="qr-wrapper" :style="{ maxWidth: size + 'px' }">
                    <canvas ref="qrCanvas" class="qr-code"></canvas>
                </div>

                <div class="controls">
                    <label>
                        Розмір QR-коду:
                        <input type="range" v-model="size" min="100" max="500">
                        {{ size }}px
                    </label>

                    <div class="color-controls">
                        <label>
                            Колір:
                            <input type="color" v-model="colorDark" @input="generateQR">
                        </label>
                        <label>
                            Фон:
                            <input type="color" v-model="colorLight" @input="generateQR">
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" v-model="isDynamic">
                            Зробити динамічним (зі статистикою)
                        </label>
                    </div>

                    <div class="action-buttons">
                        <button @click="downloadQR" class="download-btn">
                            Завантажити PNG
                        </button>
                        <button @click="downloadSVG" class="download-btn secondary">
                            Завантажити SVG
                        </button>
                        <button @click="saveToHistory" class="download-btn save-btn">
                            Зберегти в історію
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="placeholder">
                Введіть текст вище, щоб згенерувати QR-код
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, watch, onMounted, computed} from 'vue'
import QRCode from 'qrcode'
import {router} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";

const inputText = ref('')
const size = ref(200)
const qrCanvas = ref(null)
const colorDark = ref('#000000')
const colorLight = ref('#ffffff')
const isDynamic = ref(false)

const MAX_TEXT_LENGTH = 500
const WARNING_THRESHOLD = 300

const showWarning = computed(() => inputText.value.length > WARNING_THRESHOLD)

const saveToHistory = () => {
    router.post('/qr', {
        content: inputText.value,
        size: size.value,
        color_dark: colorDark.value,
        color_light: colorLight.value,
        is_dynamic: isDynamic.value,
    })
}

const generateQR = async () => {
    if (!inputText.value || !qrCanvas.value) return
    try {
        await QRCode.toCanvas(qrCanvas.value, inputText.value, {
            width: Math.min(size.value, 800),
            margin: 2,
            color: {dark: colorDark.value, light: colorLight.value}
        })
    } catch (err) {
        console.error('Помилка генерації QR-коду:', err)
    }
}

const downloadQR = () => {
    if (!qrCanvas.value) return
    const link = document.createElement('a')
    link.href = qrCanvas.value.toDataURL('image/png')
    link.download = `qr-code-${Date.now()}.png`
    link.click()
}

const downloadSVG = async () => {
    if (!inputText.value) return
    try {
        const svg = await QRCode.toString(inputText.value, {
            type: 'svg',
            color: {dark: colorDark.value, light: colorLight.value}
        })
        const blob = new Blob([svg], {type: 'image/svg+xml'})
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `qr-code-${Date.now()}.svg`
        link.click()
        URL.revokeObjectURL(url)
    } catch (err) {
        console.error('Помилка генерації SVG:', err)
    }
}

const handleInput = async () => {
    if (inputText.value.length > MAX_TEXT_LENGTH) {
        inputText.value = inputText.value.substring(0, MAX_TEXT_LENGTH)
    }
    await generateQR()
}

watch(size, generateQR)
onMounted(() => inputText.value && generateQR())
</script>

<style scoped>
.checkbox {
    margin: 10px 0;
    color: #333;
}

.download-btn.save-btn {
    background-color: #9c27b0;
}

.download-btn.save-btn:hover {
    background-color: #7b1fa2;
}

.qr-generator {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}

.input-container {
    margin-bottom: 20px;
    position: relative;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    resize: vertical;
}

textarea.error {
    border-color: #ff9800;
}

.warning-message {
    color: #ff9800;
    font-size: 14px;
    margin-top: 5px;
}

.char-count {
    font-size: 14px;
    color: #666;
    text-align: right;
    margin-top: 5px;
}

.qr-container {
    margin-top: 30px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.controls {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.download-btn {
    padding: 10px 20px;
    background-color: #42b983;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.download-btn.secondary {
    background-color: #2196f3;
}

.download-btn.secondary:hover {
    background-color: #0b7dda;
}

.placeholder {
    color: #666;
    font-style: italic;
    margin-top: 30px;
}
</style>
