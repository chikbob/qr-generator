<template>
    <AppLayout>
        <div class="qr-generator">
            <h2 style="margin: 2rem 0; font-size: 1.8rem">Генератор QR-кодів</h2>

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
                        <input type="range" v-model="size" min="100" max="500" />
                        {{ size }}px
                    </label>

                    <div class="color-controls">
                        <label>
                            Колір:
                            <input type="color" v-model="colorDark" @input="generateQR" />
                        </label>
                        <label>
                            Фон:
                            <input type="color" v-model="colorLight" @input="generateQR" />
                        </label>
                    </div>

                    <div class="checkbox">
                        <label v-if="canUseDynamic">
                            <input type="checkbox" v-model="isDynamic" />
                            Зробити динамічним (зі статистикою)
                        </label>
                        <p v-else class="pro-hint">
                            🔒 Динамічні QR-коди доступні лише для користувачів з планом
                            <strong>Pro</strong> або <strong>Enterprise</strong>.
                        </p>
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
import { ref, watch, onMounted, computed } from 'vue'
import QRCode from 'qrcode'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const userPlan = computed(() => page.props.auth?.user?.plan || 'Free')

const inputText = ref('')
const size = ref(200)
const qrCanvas = ref(null)
const colorDark = ref('#000000')
const colorLight = ref('#ffffff')
const isDynamic = ref(false)

const MAX_TEXT_LENGTH = 500
const WARNING_THRESHOLD = 300

const showWarning = computed(() => inputText.value.length > WARNING_THRESHOLD)

console.log(page.props.auth?.user)
// Можно ли использовать динамические QR-коды
const canUseDynamic = computed(() => ['Pro', 'Enterprise'].includes(userPlan.value))

// Следим за изменением isDynamic и запрещаем ставить его, если нет доступа
watch(isDynamic, (val) => {
    if (val && !canUseDynamic.value) {
        alert('Динамічні QR-коди доступні лише для користувачів з планом Pro або Enterprise.')
        isDynamic.value = false
    }
})

const saveToHistory = () => {
    if (isDynamic.value && !canUseDynamic.value) {
        alert('Динамічні QR-коди доступні лише для користувачів з планом Pro або Enterprise.')
        isDynamic.value = false
        return
    }

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
            color: { dark: colorDark.value, light: colorLight.value },
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
            color: { dark: colorDark.value, light: colorLight.value },
        })
        const blob = new Blob([svg], { type: 'image/svg+xml' })
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
.qr-generator {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1.5rem 1.5rem;
    background: #fff;
    border-radius: 8px;
    text-align: center;
    color: #2c3e50;
}

h1 {
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #34495e;
}

.input-container {
    position: relative;
    margin-bottom: 20px;
}

textarea {
    width: 100%;
    padding: 12px;
    border: 1.5px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

textarea.error {
    border-color: #ff9800;
}

.warning-message {
    color: #ff9800;
    font-size: 0.875rem;
    margin-top: 6px;
    text-align: left;
}

.char-count {
    font-size: 0.875rem;
    color: #666;
    text-align: right;
    margin-top: 5px;
}

.qr-container {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.qr-wrapper {
    margin: 0 auto 1rem;
    max-width: 100%;
}

.qr-code {
    max-width: 100%;
    height: auto;
}

.controls {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.controls label {
    font-weight: 600;
    color: #34495e;
}

input[type="range"] {
    width: 180px;
    cursor: pointer;
}

.color-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 0.5rem;
}

.color-controls label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.checkbox {
    color: #333;
    margin-top: 0.5rem;
}

.pro-hint {
    font-size: 0.9rem;
    color: #888;
    margin-top: 0.3rem;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.download-btn {
    background-color: #42b983;
    padding: 10px 22px;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
}

.download-btn:hover {
    background-color: #369d6f;
}

.download-btn.secondary {
    background-color: #2196f3;
}

.download-btn.secondary:hover {
    background-color: #0b7dda;
}

.download-btn.save-btn {
    background-color: #9c27b0;
}

.download-btn.save-btn:hover {
    background-color: #7b1fa2;
}

.placeholder {
    margin-top: 3rem;
    font-style: italic;
    color: #666;
    font-size: 1rem;
}
</style>
