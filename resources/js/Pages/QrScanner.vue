<template>
    <AppLayout>
        <div class="qr-scanner-container">
            <h1>Сканер QR-кодів</h1>

            <div class="scanner-wrapper">
                <div class="video-container" :class="{ 'active': isScanning }">
                    <video ref="video" autoplay playsinline class="scanner-video"></video>
                    <div class="scan-frame"></div>
                    <div v-if="!isScanning" class="scanner-placeholder">
                        <div class="placeholder-icon">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M4,4H7L9,2H15L17,4H20A2,2 0 0,1 22,6V18A2,2 0 0,1 20,20H4A2,2 0 0,1 2,18V6A2,2 0 0,1 4,4M12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17A5,5 0 0,0 17,12A5,5 0 0,0 12,7M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9Z"/>
                            </svg>
                        </div>
                        <p>Натисніть "Почати сканування"</p>
                    </div>
                    <div v-if="isScanning" class="scanning-hint">
                        Наведіть камеру на QR-код
                    </div>
                </div>

                <div class="scanner-controls">
                    <div class="camera-controls">
                        <div class="camera-selector" v-if="devices.length > 0">
                            <label for="camera-select">Оберіть камеру:</label>
                            <select
                                id="camera-select"
                                v-model="selectedDeviceId"
                                class="device-select"
                                :disabled="isScanning"
                            >
                                <option
                                    v-for="device in devices"
                                    :key="device.deviceId"
                                    :value="device.deviceId"
                                >
                                    {{ getCameraLabel(device) }}
                                </option>
                            </select>
                        </div>

                        <button
                            @click="refreshCameras"
                            class="action-btn refresh"
                            :disabled="isScanning || isLoadingDevices"
                        >
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
                            </svg>
                            Оновити
                        </button>
                    </div>

                    <button
                        @click="toggleScan"
                        :class="['scan-btn', isScanning ? 'stop' : 'start']"
                        :disabled="isLoadingDevices || devices.length === 0"
                    >
                        <span v-if="isLoadingDevices">Завантаження...</span>
                        <span v-else>{{ isScanning ? 'Зупинити' : 'Почати сканування' }}</span>
                    </button>
                </div>
            </div>

            <div v-if="result" class="scan-result">
                <h3>Результат сканування:</h3>
                <div class="result-content">
        <textarea
            ref="resultText"
            v-model="result"
            readonly
            class="result-textarea"
            rows="4"
        ></textarea>
                    <div class="result-actions">
                        <button @click="copyResult" class="action-btn copy">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M19,21H8V7H19M19,5H8A2,2 0 0,0 6,7V21A2,2 0 0,0 8,23H19A2,2 0 0,0 21,21V7A2,2 0 0,0 19,5M16,1H4A2,2 0 0,0 2,3V17H4V3H16V1Z"/>
                            </svg>
                            Копіювати
                        </button>
                        <button
                            v-if="isValidUrl(result)"
                            @click="openUrl"
                            class="action-btn open"
                        >
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/>
                            </svg>
                            Відкрити
                        </button>
                        <button @click="clearResult" class="action-btn clear">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                            </svg>
                            Очистити
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="error" class="error-message">
                <p>{{ error }}</p>
                <button @click="clearError" class="error-btn">OK</button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, onMounted, onUnmounted} from 'vue'
import {BrowserQRCodeReader} from '@zxing/library'
import AppLayout from "@/Layouts/AppLayout.vue";

const video = ref(null)
const result = ref(null)
const error = ref(null)
const isScanning = ref(false)
const devices = ref([])
const selectedDeviceId = ref(null)
const isLoadingDevices = ref(false)
const cameraPermission = ref('prompt')

let controls = null
let codeReader = null

const getCameraLabel = (device) => {
    if (device.label) {
        return device.label
            .replace(/\([^)]*\)/g, '')
            .replace(/facing\s\w+/i, '')
            .trim() || `Камера ${device.deviceId.slice(0, 8)}`
    }
    return `Камера ${device.deviceId.slice(0, 8)}`
}

const checkCameraPermission = async () => {
    try {
        if (!navigator.permissions || !navigator.permissions.query) {
            return 'prompt'
        }

        const permissionStatus = await navigator.permissions.query({name: 'camera'})
        cameraPermission.value = permissionStatus.state

        permissionStatus.onchange = () => {
            cameraPermission.value = permissionStatus.state
            if (permissionStatus.state === 'granted') {
                getAvailableCameras()
            } else {
                stopScan()
                error.value = 'Дозвіл на камеру не надано. Будь ласка, надайте доступ у налаштуваннях браузера.'
            }
        }

        return permissionStatus.state
    } catch (err) {
        console.error('Помилка перевірки дозволу:', err)
        return 'prompt'
    }
}

const requestCameraAccess = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: {ideal: 1280},
                height: {ideal: 720}
            }
        })

        stream.getTracks().forEach(track => track.stop())
        cameraPermission.value = 'granted'
        return true
    } catch (err) {
        console.error('Помилка доступу до камери:', err)
        cameraPermission.value = 'denied'

        if (err.name === 'NotAllowedError') {
            error.value = 'Доступ до камери заблоковано. Будь ласка, надайте дозвіл у налаштуваннях браузера.'
        } else if (err.name === 'NotFoundError') {
            error.value = 'Камера не знайдена. Переконайтесь, що камера підключена.'
        } else {
            error.value = 'Помилка доступу до камери: ' + (err.message || 'Невідома помилка')
        }

        return false
    }
}

const getAvailableCameras = async () => {
    if (cameraPermission.value === 'denied') {
        error.value = 'Доступ до камери заблоковано. Будь ласка, надайте дозвіл у налаштуваннях браузера.'
        return
    }

    if (cameraPermission.value !== 'granted') {
        const hasAccess = await requestCameraAccess()
        if (!hasAccess) return
    }

    isLoadingDevices.value = true
    error.value = null

    try {
        codeReader = new BrowserQRCodeReader()
        const videoDevices = await codeReader.listVideoInputDevices()

        if (!videoDevices || videoDevices.length === 0) {
            error.value = 'Камери не виявлено. Переконайтесь, що камера підключена.'
            return
        }

        devices.value = videoDevices

        // Спроба знайти задню камеру
        const backCamera = videoDevices.find(device =>
            device.label.toLowerCase().includes('back') ||
            device.label.toLowerCase().includes('rear')
        )

        selectedDeviceId.value = backCamera?.deviceId || videoDevices[0].deviceId

    } catch (err) {
        console.error('Помилка отримання камер:', err)
        error.value = 'Помилка отримання списку камер: ' + (err.message || 'Невідома помилка')
    } finally {
        isLoadingDevices.value = false
    }
}

const refreshCameras = async () => {
    await stopScan()
    await getAvailableCameras()
}

const toggleScan = async () => {
    if (isScanning.value) {
        await stopScan()
    } else {
        await startScan()
    }
}

const startScan = async () => {
    if (!selectedDeviceId.value) {
        await getAvailableCameras()
        if (!selectedDeviceId.value) return
    }

    try {
        codeReader = new BrowserQRCodeReader({
            tryHarder: true,
            timeout: 10000
        })

        result.value = null
        isScanning.value = true
        error.value = null

        controls = await codeReader.decodeFromVideoDevice(
            selectedDeviceId.value,
            video.value,
            (scanResult, err) => {
                if (err) {
                    const errMsg = err?.message || String(err)
                    if (!errMsg.includes('NotFoundException')) {
                        console.error('Помилка сканування:', err)
                        error.value = 'Помилка сканування: ' + errMsg
                    }
                    return
                }

                if (scanResult) {
                    result.value = scanResult.getText()
                    stopScan()
                }
            }
        )
    } catch (err) {
        console.error('Помилка ініціалізації сканування:', err)
        error.value = 'Помилка при запуску сканування: ' +
            (err.message || 'Невідома помилка')
        isScanning.value = false
    }
}

const stopScan = async () => {
    try {
        if (controls) {
            controls.stop()
            controls = null
        }
        if (codeReader) {
            codeReader.reset()
            codeReader = null
        }
    } catch (e) {
        console.error('Помилка зупинки сканування:', e)
    } finally {
        isScanning.value = false
    }
}

const copyResult = async () => {
    try {
        await navigator.clipboard.writeText(result.value)
        error.value = 'Текст скопійовано в буфер обміну!'
        setTimeout(() => error.value = null, 2000)
    } catch (err) {
        console.error('Помилка копіювання:', err)
        error.value = 'Не вдалося скопіювати текст: ' + (err.message || 'Невідома помилка')
    }
}

const clearResult = () => {
    result.value = null
}

const isValidUrl = (str) => {
    try {
        new URL(str)
        return true
    } catch {
        return false
    }
}

const openUrl = () => {
    if (isValidUrl(result.value)) {
        window.open(result.value, '_blank', 'noopener,noreferrer')
    }
}

const clearError = () => {
    error.value = null
}

onMounted(async () => {
    await checkCameraPermission()
    await getAvailableCameras()
})

onUnmounted(() => {
    stopScan()
})
</script>

<style scoped>
.qr-scanner-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    font-weight: 600;
}

.scanner-wrapper {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.video-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    max-height: 60vh;
    background: #000;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.video-container.active {
    background: transparent;
}

.scanner-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.scan-frame {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    height: 80%;
    border: 3px solid rgba(76, 175, 80, 0.7);
    border-radius: 8px;
    box-shadow: 0 0 0 100vmax rgba(0, 0, 0, 0.6);
    z-index: 1;
}

.scanner-placeholder {
    position: absolute;
    text-align: center;
    color: white;
    z-index: 0;
    padding: 20px;
}

.placeholder-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    opacity: 0.8;
}

.placeholder-icon svg {
    fill: currentColor;
    width: 100%;
    height: 100%;
}

.scanning-hint {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    text-align: center;
    color: white;
    background: rgba(0, 0, 0, 0.7);
    padding: 12px;
    z-index: 2;
    font-size: 0.9rem;
}

.scanner-controls {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

.camera-controls {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.camera-selector {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.camera-selector label {
    font-size: 0.85rem;
    color: #495057;
    font-weight: 500;
}

.device-select {
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    background: white;
    font-size: 0.9rem;
    width: 100%;
    transition: border-color 0.2s;
}

.device-select:focus {
    outline: none;
    border-color: #42b983;
    box-shadow: 0 0 0 2px rgba(66, 185, 131, 0.2);
}

.scan-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.scan-btn.start {
    background-color: #42b983;
    color: white;
}

.scan-btn.stop {
    background-color: #f44336;
    color: white;
}

.scan-btn:hover:not(:disabled) {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.scan-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    box-shadow: none;
}

.action-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.action-btn svg {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

.action-btn.copy {
    background-color: #2196f3;
    color: white;
}

.action-btn.open {
    background-color: #4caf50;
    color: white;
}

.action-btn.clear {
    background-color: #f44336;
    color: white;
}

.action-btn.refresh {
    background-color: #ff9800;
    color: white;
}

.action-btn:hover:not(:disabled) {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.action-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.scan-result {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    margin-top: 20px;
}

.scan-result h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #2c3e50;
    font-weight: 500;
}

.result-content {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.result-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    resize: vertical;
    font-family: inherit;
    font-size: 0.95rem;
    line-height: 1.5;
}

.result-textarea:focus {
    outline: none;
    border-color: #42b983;
    box-shadow: 0 0 0 2px rgba(66, 185, 131, 0.2);
}

.result-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.error-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    z-index: 100;
    text-align: center;
    max-width: 90%;
    width: 400px;
}

.error-message p {
    margin: 0 0 15px;
    color: #d32f2f;
    font-size: 1rem;
}

.error-btn {
    padding: 8px 16px;
    background: #f44336;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
}

.permission-hint {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.permission-hint p {
    margin: 0;
    color: #1976d2;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .qr-scanner-container {
        padding: 15px;
    }

    .video-container {
        max-height: 50vh;
    }

    .scanner-controls {
        flex-direction: column;
    }

    .camera-controls {
        flex-direction: column;
        gap: 12px;
    }

    .device-select, .scan-btn, .action-btn {
        width: 100%;
    }

    .result-actions {
        justify-content: center;
    }

    .error-message {
        width: 90%;
        padding: 15px;
    }
}
</style>
