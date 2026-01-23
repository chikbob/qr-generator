<template>
    <AppLayout>
        <div class="qr-scanner-container">
            <h1>{{ $t('qrScanner.title') }}</h1>

            <div class="scanner-wrapper">
                <div class="video-container" :class="{ 'active': isScanning }">
                    <video ref="video" autoplay playsinline class="scanner-video"></video>
                    <div class="scan-frame"></div>

                    <div v-if="!isScanning" class="scanner-placeholder">
                        <div class="placeholder-icon">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M4,4H7L9,2H15L17,4H20A2,2 0 0,1 22,6V18A2,2 0 0,1 20,20H4A2,2 0 0,1 2,18V6A2,2 0 0,1 4,4M12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17A5,5 0 0,0 17,12A5,5 0 0,0 12,7M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9Z"
                                />
                            </svg>
                        </div>
                        <p>{{ $t('qrScanner.placeholderText') }}</p>
                    </div>

                    <div v-if="isScanning" class="scanning-hint">
                        {{ $t('qrScanner.scanningHint') }}
                    </div>
                </div>

                <div class="scanner-controls">
                    <div class="camera-controls">
                        <div class="camera-selector" v-if="devices.length > 0">
                            <label for="camera-select">{{ $t('qrScanner.cameraLabel') }}</label>
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
                                    d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"
                                />
                            </svg>
                            {{ $t('qrScanner.refreshButton') }}
                        </button>
                    </div>

                    <button
                        @click="toggleScan"
                        :class="['scan-btn', isScanning ? 'stop' : 'start']"
                        :disabled="isLoadingDevices || devices.length === 0"
                    >
                        <span v-if="isLoadingDevices">{{ $t('qrScanner.loadingDevices') }}</span>
                        <span v-else>
              {{ isScanning ? $t('qrScanner.stopScan') : $t('qrScanner.startScan') }}
            </span>
                    </button>
                </div>
            </div>

            <div v-if="result" class="scan-result">
                <h3>{{ $t('qrScanner.scanResultTitle') }}</h3>
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
                                    d="M19,21H8V7H19M19,5H8A2,2 0 0,0 6,7V21A2,2 0 0,0 8,23H19A2,2 0 0,0 21,21V7A2,2 0 0,0 19,5M16,1H4A2,2 0 0,0 2,3V17H4V3H16V1Z"
                                />
                            </svg>
                            {{ $t('qrScanner.copyButton') }}
                        </button>
                        <button
                            v-if="isValidUrl(result)"
                            @click="openUrl"
                            class="action-btn open"
                        >
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"
                                />
                            </svg>
                            {{ $t('qrScanner.openButton') }}
                        </button>
                        <button @click="clearResult" class="action-btn clear">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6Z"
                                />
                            </svg>
                            {{ $t('qrScanner.clearButton') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="error" class="error-message">
                <p>{{ error }}</p>
                <button @click="clearError" class="error-btn">{{ $t('qrScanner.errorOk') }}</button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, onMounted, onUnmounted} from 'vue'
import {
    BrowserQRCodeReader,
    NotFoundException,
    ChecksumException,
    FormatException
} from '@zxing/library'
import AppLayout from '@/Layouts/AppLayout.vue'
import {useI18n} from '@/lang/useI18n'

const {t: $t} = useI18n()

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
        return (
            device.label
                .replace(/\([^)]*\)/g, '')
                .replace(/facing\s\w+/i, '')
                .trim() || `Camera ${device.deviceId.slice(0, 8)}`
        )
    }
    return `Camera ${device.deviceId.slice(0, 8)}`
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
                error.value = $t('qrScanner.errorCameraDenied')
            }
        }
        return permissionStatus.state
    } catch (err) {
        console.error('Camera permission error:', err)
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

        stream.getTracks().forEach((track) => track.stop())
        cameraPermission.value = 'granted'
        return true
    } catch (err) {
        console.error('Camera access error:', err)
        cameraPermission.value = 'denied'

        if (err.name === 'NotAllowedError') {
            error.value = $t('qrScanner.errorCameraDenied')
        } else if (err.name === 'NotFoundError') {
            error.value = $t('qrScanner.cameraNotFound')
        } else {
            error.value = 'Camera access error: ' + (err.message || 'Unknown error')
        }
        return false
    }
}

const getAvailableCameras = async () => {
    if (cameraPermission.value === 'denied') {
        error.value = $t('qrScanner.errorCameraDenied')
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
            error.value = $t('qrScanner.cameraNotFound')
            return
        }

        devices.value = videoDevices

        const backCamera = videoDevices.find((device) =>
            device.label.toLowerCase().includes('back') || device.label.toLowerCase().includes('rear')
        )

        selectedDeviceId.value = backCamera?.deviceId || videoDevices[0].deviceId
    } catch (err) {
        console.error('Error getting cameras:', err)
        error.value = $t('qrScanner.errorLoadingDevices') + (err.message || '')
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
            delayBetweenScanAttempts: 500
        })

        result.value = null
        isScanning.value = true
        error.value = null

        controls = await codeReader.decodeFromVideoDevice(
            selectedDeviceId.value,
            video.value,
            (scanResult, err) => {
                if (err) {
                    if (
                        err instanceof NotFoundException ||
                        err instanceof ChecksumException ||
                        err instanceof FormatException
                    ) {
                        return
                    }

                    console.error('Scan error:', err)
                    error.value = $t('qrScanner.errorScanning') + ': ' + (err.message || '')
                    return
                }

                if (scanResult) {
                    result.value = scanResult.getText()
                    stopScan()
                }
            }
        )
    } catch (err) {
        console.error('Scan init error:', err)
        error.value = $t('qrScanner.errorScanning') + ': ' + (err.message || 'Unknown error')
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
        console.error('Stop scan error:', e)
    } finally {
        isScanning.value = false
    }
}

const copyResult = async () => {
    try {
        await navigator.clipboard.writeText(result.value)
        error.value = $t('qrScanner.copySuccess')
        setTimeout(() => (error.value = null), 2000)
    } catch (err) {
        console.error('Copy error:', err)
        error.value = $t('qrScanner.copyFail') + ': ' + (err.message || 'Unknown error')
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
    max-width: 900px;
    margin: 2rem auto;
    background: #fff;
    border-radius: 8px;
    color: #2c3e50;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

h1 {
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #34495e;
    text-align: center;
}

.scanner-wrapper {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.video-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    max-height: 60vh;
    background: #f5f5f5;
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
    color: #666;
    z-index: 0;
    padding: 20px;
    font-style: italic;
}

.placeholder-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    opacity: 0.8;
    color: #34495e;
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
    color: #2b5cff;
    background: rgba(255 255 255 / 0.9);
    padding: 12px;
    z-index: 2;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 4px;
    box-shadow: 0 0 8px rgba(43, 92, 255, 0.3);
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
    border: 1px solid #ddd;
    font-size: 0.9rem;
    width: 100%;
    transition: border-color 0.2s;
}

.device-select:focus {
    outline: none;
    border-color: #2b5cff;
    box-shadow: 0 0 0 2px rgba(43, 92, 255, 0.2);
}

.scan-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: white;
}

.scan-btn.start {
    background-color: #2b5cff;
}

.scan-btn.start:hover:not(:disabled) {
    background-color: #1a3fcc;
}

.scan-btn.stop {
    background-color: #f44336;
}

.scan-btn.stop:hover:not(:disabled) {
    background-color: #d32f2f;
}

.scan-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.action-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    color: white;
}

.action-btn svg {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

.action-btn.copy {
    background-color: #2196f3;
}

.action-btn.copy:hover:not(:disabled) {
    background-color: #0b7dda;
}

.action-btn.open {
    background-color: #4caf50;
}

.action-btn.open:hover:not(:disabled) {
    background-color: #388e3c;
}

.action-btn.clear {
    background-color: #f44336;
}

.action-btn.clear:hover:not(:disabled) {
    background-color: #d32f2f;
}

.action-btn.refresh {
    background-color: #ff9800;
    color: white;
}

.action-btn.refresh:hover:not(:disabled) {
    background-color: #e68a00;
}

.scan-result {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    margin-top: 20px;
    color: #2c3e50;
}

.scan-result h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #34495e;
    font-weight: 700;
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
    border: 1px solid #ddd;
    border-radius: 6px;
    resize: vertical;
    font-family: inherit;
    font-size: 1rem;
    line-height: 1.5;
    color: #34495e;
}

.result-textarea:focus {
    outline: none;
    border-color: #2b5cff;
    box-shadow: 0 0 0 2px rgba(43, 92, 255, 0.2);
}

.result-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.error-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    z-index: 100;
    text-align: center;
    max-width: 90%;
    width: 400px;
    color: #b80000;
}

.error-message p {
    margin: 0 0 15px;
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

    .device-select,
    .scan-btn,
    .action-btn {
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
