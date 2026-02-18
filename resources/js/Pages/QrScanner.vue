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
import {useI18n} from '@/Lang/useI18n'

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

<style lang="scss" scoped>
$accent: #e095bc;
$accent-dark: #bd6592;
$accent-soft: #fce7f3;

$text-main: #0f172a;
$text-secondary: #475569;
$border: #e2e8f0;
$bg-soft: #f8fafc;

.qr-scanner-container {
    max-width: 900px;
    margin: 2rem auto;
    background: #fff;
    border-radius: 24px;
    color: $text-main;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
    padding: 2rem 2.5rem;

    h1 {
        font-weight: 800;
        font-size: 2.4rem;
        margin-bottom: 2rem;
        text-align: center;
        background: linear-gradient(135deg, $accent, $accent-dark);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .scanner-wrapper {
        background: #ffffff;
        border-radius: 24px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);

        .video-container {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            max-height: 60vh;
            border-radius: 24px;
            overflow: hidden;
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: inset 0 0 12px rgba(219, 39, 119, 0.12);

            &.active {
                background: transparent;
            }

            .scanner-video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                border-radius: 24px;
            }

            .scan-frame {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 80%;
                height: 80%;
                border: 2.5px solid $accent-dark;
                border-radius: 24px;
                box-shadow: 0 0 0 100vmax rgba(219, 39, 119, 0.1);
                z-index: 1;
            }

            .scanner-placeholder {
                position: absolute;
                text-align: center;
                color: $accent-dark;
                z-index: 0;
                padding: 20px;
                font-style: italic;
                font-weight: 600;

                .placeholder-icon {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 20px;
                    opacity: 0.75;
                    color: $accent-dark;

                    svg {
                        fill: currentColor;
                        width: 100%;
                        height: 100%;
                    }
                }
            }

            .scanning-hint {
                position: absolute;
                bottom: 20px;
                left: 0;
                right: 0;
                text-align: center;
                color: $accent-dark;
                background: rgba(255, 255, 255, 0.95);
                padding: 10px 14px;
                z-index: 2;
                font-size: 1rem;
                font-weight: 600;
                border-radius: 12px;
                box-shadow: 0 0 8px rgba(219, 39, 119, 0.25);
            }
        }

        .scanner-controls {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
            margin-top: 20px;

            .camera-controls {
                display: flex;
                gap: 1rem;
                align-items: flex-end;
                flex-wrap: wrap;

                .camera-selector {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;

                    label {
                        font-size: 0.95rem;
                        color: $text-secondary;
                        font-weight: 600;
                    }

                    .device-select {
                        padding: 10.5px 18px;
                        border-radius: 14px;
                        border: 1.5px solid $border;
                        font-size: 1rem;
                        width: 100%;
                        transition: border-color 0.25s ease, box-shadow 0.25s ease;
                        cursor: pointer;
                        background-color: $bg-soft;
                        color: $accent-dark;

                        &:focus {
                            outline: none;
                            border-color: $accent;
                            box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.15);
                        }
                    }
                }
            }

            .scan-btn {
                padding: 14px 34px;
                border: none;
                border-radius: 999px;
                font-size: 1.05rem;
                font-weight: 700;
                cursor: pointer;
                transition: background-color 0.3s ease, box-shadow 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                color: #fff;
                box-shadow: 0 14px 32px rgba(236, 72, 153, 0.45);
                background: linear-gradient(135deg, $accent, $accent-dark);

                &.start:hover:not(:disabled) {
                    transform: translateY(-2px) scale(1.03);
                    box-shadow: 0 20px 45px rgba(236, 72, 153, 0.55);
                }

                &.stop {
                    background-color: #ef4444;
                    box-shadow: 0 3px 10px rgba(239, 68, 68, 0.25);

                    &:hover:not(:disabled) {
                        background-color: #b91c1c;
                        box-shadow: 0 6px 14px rgba(185, 28, 28, 0.35);
                    }
                }

                &:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }
            }

            .action-btn {
                padding: 12px 28px;
                border: none;
                border-radius: 999px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.3s ease, box-shadow 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #fff;
                box-shadow: 0 14px 32px rgba(236, 72, 153, 0.35);
                background: linear-gradient(135deg, $accent, $accent-dark);

                svg {
                    width: 18px;
                    height: 18px;
                    fill: currentColor;
                }

                &.copy:hover:not(:disabled),
                &.open:hover:not(:disabled),
                &.clear:hover:not(:disabled),
                &.refresh:hover:not(:disabled) {
                    filter: brightness(0.9);
                    box-shadow: 0 20px 45px rgba(236, 72, 153, 0.55);
                }

                &.copy {
                    background-color: $accent-dark;
                }

                &.open {
                    background-color: #2563eb;
                }

                &.clear {
                    background-color: #ef4444;
                }

                &.refresh {
                    background-color: #fbbf24;
                    color: #fff;
                    box-shadow: none;
                }
            }
        }
    }

    .scan-result {
        background: #ffffff;
        border-radius: 24px;
        padding: 2rem 2.5rem;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
        margin-top: 2rem;
        color: $text-main;

        h3 {
            margin-top: 0;
            margin-bottom: 1rem;
            color: $accent-dark;
            font-weight: 700;
            font-size: 1.6rem;
        }

        .result-content {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .result-textarea {
            width: 100%;
            min-height: 110px;
            padding: 16px 20px;
            border: 1.5px solid $border;
            border-radius: 14px;
            resize: vertical;
            font-family: inherit;
            font-size: 1rem;
            line-height: 1.5;
            color: $text-secondary;
            background-color: $bg-soft;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;

            &:focus {
                outline: none;
                border-color: $accent;
                box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.15);
            }
        }

        .result-actions {
            display: flex;
            gap: 1.2rem;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
    }

    .error-message {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        padding: 1.5rem 2rem;
        border-radius: 24px;
        box-shadow: 0 30px 60px rgba(219, 39, 119, 0.3);
        z-index: 100;
        text-align: center;
        max-width: 90%;
        width: 400px;
        color: #9b1c47;
        font-weight: 600;

        p {
            margin: 0 0 1rem;
            font-size: 1.2rem;
        }

        .error-btn {
            padding: 12px 28px;
            background: $accent-dark;
            color: #ffffff;
            border: none;
            border-radius: 24px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;

            &:hover {
                background: #8a3c3f;
                box-shadow: 0 30px 60px rgba(138, 60, 63, 0.35);
            }
        }
    }

    @media (max-width: 768px) {
        padding: 1rem 1.5rem;

        .scanner-wrapper {
            .video-container {
                max-height: 50vh;
            }

            .scanner-controls {
                flex-direction: column;

                .camera-controls {
                    flex-direction: column;
                    gap: 1rem;
                }

                .device-select,
                .scan-btn,
                .action-btn {
                    width: 100%;
                }
            }
        }

        .scan-result {
            .result-actions {
                justify-content: center;
            }
        }

        .error-message {
            width: 90%;
            padding: 1rem 1.25rem;
        }
    }
}
</style>
