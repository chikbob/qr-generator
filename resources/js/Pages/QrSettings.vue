<template>
    <div class="qr-app">
        <!-- Навигационное меню -->
        <nav class="nav-menu">
            <router-link to="/" class="nav-link">Главная</router-link>
            <router-link to="/generator" class="nav-link">Генератор</router-link>
            <router-link to="/scanner" class="nav-link active">Сканер</router-link>
            <router-link to="/history" class="nav-link">История</router-link>
        </nav>

        <div class="scanner-container">
            <h1>Сканер QR-кодов</h1>

            <div class="scanner-box">
                <div class="video-wrapper">
                    <video ref="video" autoplay playsinline class="scanner-video" v-show="isScanning"></video>
                    <div v-if="!isScanning" class="scanner-placeholder">
                        <p>Нажмите: "Начать сканирование"</p>
                    </div>
                    <div class="scan-frame" v-show="isScanning"></div>
                </div>

                <div class="scanner-controls">
                    <button @click="toggleScan" class="scan-btn">
                        {{ isScanning ? 'Остановить сканирование' : 'Начать сканирование' }}
                    </button>

                    <select v-model="selectedDeviceId" class="camera-select" v-if="devices.length > 1">
                        <option v-for="device in devices" :key="device.deviceId" :value="device.deviceId">
                            {{ device.label || `Камера ${device.deviceId}` }}
                        </option>
                    </select>
                </div>

                <div v-if="result" class="scan-result">
                    <h3>Результат сканирования:</h3>
                    <div class="result-box">{{ result }}</div>
                    <div class="result-actions">
                        <button @click="copyResult" class="action-btn">Копировать</button>
                        <button @click="openUrl" class="action-btn" v-if="isValidUrl(result)">Открыть</button>
                        <button @click="clearResult" class="action-btn">Очистить</button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="app-footer">
            QR Code Generator © {{ new Date().getFullYear() }}
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { BrowserQRCodeReader } from '@zxing/library'

const video = ref(null)
const result = ref('')
const isScanning = ref(false)
const devices = ref([])
const selectedDeviceId = ref('')

let codeReader = null
let controls = null

onMounted(async () => {
    try {
        codeReader = new BrowserQRCodeReader()
        const videoDevices = await codeReader.listVideoInputDevices()
        devices.value = videoDevices
        if (videoDevices.length > 0) {
            selectedDeviceId.value = videoDevices[0].deviceId
        }
    } catch (err) {
        console.error('Ошибка получения камер:', err)
    }
})

const toggleScan = async () => {
    if (isScanning.value) {
        stopScan()
    } else {
        await startScan()
    }
}

const startScan = async () => {
    try {
        codeReader = new BrowserQRCodeReader()
        result.value = ''
        isScanning.value = true

        controls = await codeReader.decodeFromVideoDevice(
            selectedDeviceId.value,
            video.value,
            (scanResult, err) => {
                if (scanResult) {
                    result.value = scanResult.text
                    stopScan()
                }
                if (err && !err.message.includes('NotFoundException')) {
                    console.error(err)
                }
            }
        )
    } catch (err) {
        console.error('Ошибка сканирования:', err)
        isScanning.value = false
    }
}

const stopScan = () => {
    if (controls) {
        controls.stop()
        controls = null
    }
    isScanning.value = false
}

const copyResult = async () => {
    try {
        await navigator.clipboard.writeText(result.value)
    } catch (err) {
        console.error('Ошибка копирования:', err)
    }
}

const openUrl = () => {
    if (isValidUrl(result.value)) {
        window.open(result.value, '_blank')
    }
}

const clearResult = () => {
    result.value = ''
}

const isValidUrl = (str) => {
    try {
        new URL(str)
        return true
    } catch {
        return false
    }
}

onUnmounted(() => {
    stopScan()
})
</script>

<style scoped>
.qr-app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.nav-menu {
    display: flex;
    justify-content: center;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 30px;
}

.nav-link {
    margin: 0 15px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    padding: 5px 10px;
}

.nav-link.active {
    color: #42b983;
    border-bottom: 2px solid #42b983;
}

.scanner-container {
    flex: 1;
    text-align: center;
}

.scanner-container h1 {
    margin-bottom: 30px;
    color: #333;
}

.scanner-box {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.video-wrapper {
    position: relative;
    width: 100%;
    max-width: 500px;
    height: 300px;
    margin: 0 auto 20px;
    background: #000;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}

.scanner-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.scan-frame {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70%;
    height: 70%;
    border: 3px solid rgba(66, 185, 131, 0.7);
    border-radius: 8px;
    box-shadow: 0 0 0 100vmax rgba(0,0,0,0.5);
    z-index: 1;
}

.scanner-placeholder {
    color: white;
    padding: 20px;
}

.scanner-controls {
    margin: 20px 0;
}

.scan-btn {
    background: #42b983;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 15px;
}

.scan-btn:hover {
    background: #3aa876;
}

.camera-select {
    padding: 10px 15px;
    border-radius: 6px;
    border: 1px solid #ddd;
    min-width: 250px;
}

.scan-result {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.scan-result h3 {
    margin-bottom: 15px;
    color: #333;
}

.result-box {
    background: white;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #ddd;
    margin-bottom: 15px;
    word-break: break-all;
}

.result-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.action-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:nth-child(1) {
    background: #2196F3;
    color: white;
}

.action-btn:nth-child(2) {
    background: #4CAF50;
    color: white;
}

.action-btn:nth-child(3) {
    background: #f44336;
    color: white;
}

.action-btn:hover {
    opacity: 0.9;
}

.app-footer {
    text-align: center;
    padding: 20px;
    color: #666;
    border-top: 1px solid #eee;
    margin-top: auto;
}

@media (max-width: 768px) {
    .video-wrapper {
        height: 250px;
    }

    .result-actions {
        flex-wrap: wrap;
    }

    .action-btn {
        flex: 1;
        min-width: 100px;
    }
}
</style>
