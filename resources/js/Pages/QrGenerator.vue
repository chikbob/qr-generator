<template>
    <AppLayout>
        <div class="qr-generator">
            <h1>{{ t('qrGenerator.title') }}</h1>

            <!-- TYPE -->
            <div class="input-container">
                <select v-model="qrType">
                    <option value="text">{{ t('qrGenerator.type.text') }}</option>
                    <option value="wifi">{{ t('qrGenerator.type.wifi') }}</option>
                    <option value="contact">{{ t('qrGenerator.type.contact') }}</option>
                    <option value="email">{{ t('qrGenerator.type.email') }}</option>
                    <option value="phone">{{ t('qrGenerator.type.phone') }}</option>
                    <option value="sms">{{ t('qrGenerator.type.sms') }}</option>
                    <option value="location">{{ t('qrGenerator.type.location') }}</option>
                    <option value="pdf">{{ t('qrGenerator.type.pdf') }}</option>
                </select>
            </div>

            <!-- TEXT -->
            <div v-if="qrType === 'text'" class="input-container">
                <textarea
                    v-model="qrData.text"
                    rows="4"
                    :placeholder="t('qrGenerator.textPlaceholder')"
                />
            </div>

            <!-- WIFI -->
            <div v-if="qrType === 'wifi'" class="input-container">
                <div class="wifi-form">
                    <input
                        type="text"
                        v-model="qrData.wifi.ssid"
                        :placeholder="t('qrGenerator.wifi.ssid')"
                    />

                    <input
                        type="password"
                        v-model="qrData.wifi.password"
                        :placeholder="t('qrGenerator.wifi.password')"
                    />

                    <select v-model="qrData.wifi.encryption">
                        <option value="WPA">{{ t('qrGenerator.wifi.wpa') }}</option>
                        <option value="WEP">{{ t('qrGenerator.wifi.wep') }}</option>
                        <option value="">{{ t('qrGenerator.wifi.open') }}</option>
                    </select>
                </div>
            </div>

            <!-- CONTACT -->
            <div v-if="qrType === 'contact'" class="input-container">
                <div class="contact-form">
                    <input type="text" v-model="qrData.contact.name" :placeholder="t('qrGenerator.contact.name')"/>
                    <input type="tel" v-model="qrData.contact.phone" :placeholder="t('qrGenerator.contact.phone')"/>
                    <input type="email" v-model="qrData.contact.email" :placeholder="t('qrGenerator.contact.email')"/>
                    <input type="text" v-model="qrData.contact.company"
                           :placeholder="t('qrGenerator.contact.company')"/>
                    <input type="url" v-model="qrData.contact.website" :placeholder="t('qrGenerator.contact.website')"/>
                    <input type="text" v-model="qrData.contact.address"
                           :placeholder="t('qrGenerator.contact.address')"/>
                </div>
            </div>

            <!-- EMAIL -->
            <div v-if="qrType === 'email'" class="input-container">
                <div class="email-form">
                    <input
                        type="email"
                        v-model="qrData.email.to"
                        :placeholder="t('qrGenerator.email.to')"
                    />

                    <input
                        type="text"
                        v-model="qrData.email.subject"
                        :placeholder="t('qrGenerator.email.subject')"
                    />

                    <textarea
                        rows="3"
                        v-model="qrData.email.body"
                        :placeholder="t('qrGenerator.email.body')"
                    />
                </div>
            </div>

            <!-- PHONE -->
            <div v-if="qrType === 'phone'" class="input-container">
                <div class="phone-form">
                    <input
                        type="tel"
                        v-model="qrData.phone.number"
                        :placeholder="t('qrGenerator.phone.number')"
                    />
                </div>
            </div>

            <!-- SMS -->
            <div v-if="qrType === 'sms'" class="input-container">
                <div class="sms-form">
                    <input
                        type="tel"
                        v-model="qrData.sms.number"
                        :placeholder="t('qrGenerator.sms.number')"
                    />
                    <textarea
                        rows="3"
                        v-model="qrData.sms.message"
                        :placeholder="t('qrGenerator.sms.message')"
                    />
                </div>
            </div>

            <!-- LOCATION -->
            <div v-if="qrType === 'location'" class="input-container">
                <div class="location-form">
                    <input
                        type="text"
                        v-model="qrData.location.address"
                        :placeholder="t('qrGenerator.location.address')"
                    />
                    <select v-model="qrData.location.mapProvider">
                        <option disabled value="">{{ t('qrGenerator.location.mapProviders.placeholder') }}</option>
                        <option value="google">{{ t('qrGenerator.location.mapProviders.google') }}</option>
                        <option value="yandex">{{ t('qrGenerator.location.mapProviders.yandex') }}</option>
                    </select>
                </div>
            </div>

            <!-- PDF -->
            <div v-if="qrType === 'pdf'" class="input-container">
                <input
                    type="file"
                    accept="application/pdf"
                    @change="onPdfSelected"
                />
                <div v-if="pdfFileName">{{ pdfFileName }}</div>
            </div>

            <!-- QR RESULT -->
            <div v-if="qrContent" class="qr-container">
                <div class="qr-wrapper">
                    <canvas ref="qrCanvas"/>
                </div>

                <div class="controls">
                    <div class="size-control">
                        <span>{{ t('qrGenerator.size') }}</span>

                        <input
                            type="range"
                            v-model="size"
                            min="100"
                            max="500"
                            step="10"
                        />

                        <span class="size-value">{{ size }} px</span>
                    </div>

                    <div class="color-controls">
                        <label>
                            {{ t('qrGenerator.colorDark') }}
                            <input type="color" v-model="colorDark"/>
                        </label>

                        <label>
                            {{ t('qrGenerator.colorLight') }}
                            <input type="color" v-model="colorLight"/>
                        </label>
                    </div>

                    <label class="checkbox">
                        <input
                            type="checkbox"
                            v-model="isDynamic"
                            :disabled="!canUseDynamic"
                        />
                        {{ t('qrGenerator.dynamic') }}
                    </label>

                    <p v-if="!canUseDynamic" class="pro-hint">
                        {{ t('qrGenerator.dynamicProHint') }}
                    </p>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn save" @click="saveToHistory">
                        {{ t('qrGenerator.save') }}
                    </button>
                </div>
            </div>

            <div v-else class="placeholder">
                {{ t('qrGenerator.placeholder') }}
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed, watch} from 'vue'
import QRCode from 'qrcode'
import {router, usePage} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {useI18n} from '@/lang/useI18n'

const {t} = useI18n()
const page = usePage()

const qrCanvas = ref(null)

const qrType = ref('text')
const size = ref(200)

const colorDark = ref('#000000')
const colorLight = ref('#ffffff')

const isDynamic = ref(false)

const userPlan = computed(() => page.props.auth?.user?.plan ?? 'Free')
const canUseDynamic = computed(() =>
    ['Pro', 'Enterprise'].includes(userPlan.value)
)

const pdfFile = ref(null)
const pdfFileName = ref('')
const pdfFileUrl = ref('')

const uploadPdfFile = async (file) => {
    const formData = new FormData()
    formData.append('file', file)

    const response = await fetch('/api/upload-pdf', {
        method: 'POST',
        body: formData
    })

    if (!response.ok) throw new Error('Ошибка загрузки')

    const data = await response.json()
    return data.url // ссылка на загруженный файл
}

const onPdfSelected = async (event) => {
    const file = event.target.files[0]
    if (!file) return
    if (file.type !== 'application/pdf') {
        alert('Пожалуйста, выберите PDF файл')
        event.target.value = ''
        return
    }

    try {
        pdfFileName.value = file.name
        pdfFileUrl.value = await uploadPdfFile(file)
    } catch (e) {
        alert('Ошибка при загрузке файла')
        pdfFileName.value = ''
        pdfFileUrl.value = ''
    }
}

const qrData = ref({
    text: '',
    wifi: {ssid: '', password: '', encryption: 'WPA'},
    contact: {name: '', phone: '', email: '', company: '', website: '', address: ''},
    email: {to: '', subject: '', body: ''},
    phone: {number: ''},
    sms: {number: '', message: ''},
    location: {address: '', mapProvider: ''},
    pdf: {document: ''}
})

/* ✅ QR появляется ТОЛЬКО если есть реальные данные */
const qrContent = computed(() => {
    if (qrType.value === 'wifi') {
        const {ssid, password, encryption} = qrData.value.wifi
        if (!ssid && !password) return ''
        return `WIFI:T:${encryption};S:${ssid};P:${password};;`
    }

    if (qrType.value === 'contact') {
        const c = qrData.value.contact
        const hasData = Object.values(c).some(v => v && v.trim())
        if (!hasData) return ''

        return [
            'BEGIN:VCARD',
            'VERSION:3.0',
            c.name && `FN:${c.name}`,
            c.company && `ORG:${c.company}`,
            c.phone && `TEL:${c.phone}`,
            c.email && `EMAIL:${c.email}`,
            c.website && `URL:${c.website}`,
            c.address && `ADR:;;${c.address}`,
            'END:VCARD',
        ].filter(Boolean).join('\n')
    }

    if (qrType.value === 'email') {
        const {to, subject, body} = qrData.value.email
        if (!to) return ''

        const params = new URLSearchParams()
        if (subject) params.append('subject', subject)
        if (body) params.append('body', body)

        return `mailto:${to}${params.toString() ? '?' + params.toString() : ''}`
    }

    if (qrType.value === 'phone') {
        return qrData.value.phone.number ? `tel:${qrData.value.phone.number}` : ''
    }

    if (qrType.value === 'sms') {
        const {number, message} = qrData.value.sms
        if (!number) return ''
        return `sms:${number}?body=${encodeURIComponent(message)}`
    }

    if (qrType.value === 'location') {
        const address = qrData.value.location.address.trim()
        if (!address) return ''

        const encodedAddress = encodeURIComponent(address)
        if (qrData.value.location.mapProvider === 'yandex') {
            return `https://yandex.ru/maps/?text=${encodedAddress}`
        } else {
            return `https://www.google.com/maps/search/?api=1&query=${encodedAddress}`
        }
    }

    if (qrType.value === 'pdf') {
        if (!pdfFileUrl.value) return ''
        return pdfFileUrl.value
    }

    // Вот здесь добавляем проверку для текстового типа
    if (qrType.value === 'text') {
        const text = qrData.value.text.trim()
        if (!text) return ''
        return text
    }

    return ''
})

const openRoute = () => {
    const {latitude, longitude} = qrData.value.location
    if (!latitude || !longitude) return alert('Введите координаты')

    // Открываем Google Maps с маршрутом из текущего местоположения к координатам
    const url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(latitude)},${encodeURIComponent(longitude)}`
    window.open(url, '_blank')
}

const generateQR = async () => {
    if (!qrCanvas.value || !qrContent.value) return

    const CANVAS_SIZE = 260 // 👈 визуальный размер QR

    await QRCode.toCanvas(qrCanvas.value, qrContent.value, {
        width: CANVAS_SIZE,
        scale: size.value / 100, // 👈 масштаб вместо resize
        color: {
            dark: colorDark.value,
            light: colorLight.value,
        },
    })

}

watch([qrContent, size, colorDark, colorLight], generateQR)

const saveToHistory = async () => {
    const payloadData = qrType.value === 'text'
        ? {text: qrData.value.text}
        : qrData.value[qrType.value] ?? null

    if (!qrContent.value) {
        alert('Нет данных для генерации QR')
        return
    }

    try {
        await router.post('/qr', {
            type: qrType.value,
            content: qrContent.value,
            payload: payloadData,
            size: size.value,
            color_dark: colorDark.value,
            color_light: colorLight.value,
            is_dynamic: isDynamic.value,
        })
    } catch (error) {
        console.error('Ошибка сохранения QR:', error)
        alert('Ошибка при сохранении QR. Проверьте консоль.')
    }
}


watch(qrType, () => {
    Object.keys(qrData.value).forEach(key => {
        if (typeof qrData.value[key] === 'string') {
            qrData.value[key] = ''
        } else {
            Object.keys(qrData.value[key]).forEach(k => {
                qrData.value[key][k] = ''
            })
        }
    })
    if (qrType.value !== 'pdf') {
        // Сбрасываем pdf данные при смене типа
        pdfFile.value = null
        pdfFileName.value = ''
        if (pdfFileUrl.value) {
            URL.revokeObjectURL(pdfFileUrl.value)
            pdfFileUrl.value = ''
        }
    }
})

</script>

<style scoped lang="scss">
.qr-generator {
    max-width: 600px;
    margin: 2rem auto;
    padding: 1.5rem;
    text-align: center;
    color: #2c3e50;

    h1 {
        margin-bottom: 1.5rem;
        font-weight: 700;
    }
}

.input-container {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

$input-width: 420px;

textarea,
input,
select {
    max-width: $input-width;
    padding: 12px;
    font-size: 1rem;
    border-radius: 6px;
    border: 1.5px solid #ccc;
    font-family: inherit;
    transition: 0.2s;

    &:focus {
        outline: none;
        border-color: #42b983;
        box-shadow: 0 0 0 2px rgba(66, 185, 131, 0.15);
    }
}

textarea {
    resize: vertical;
}

select {
    appearance: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 12px center / 14px;
    min-width: 420px;
}

.wifi-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
    max-width: $input-width;
}

.qr-container {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.controls {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.color-controls {
    display: flex;
    gap: 1rem;
}

.checkbox {
    input {
        margin-right: 6px;
        transform: scale(1.1);
    }
}

.pro-hint {
    font-size: 0.9rem;
    color: #888;
}

.action-buttons {
    margin-top: 1rem;
}

.btn {
    padding: 10px 22px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    color: #fff;

    &.save {
        background: #9c27b0;

        &:hover {
            background: #7b1fa2;
        }
    }
}

.placeholder {
    margin-top: 3rem;
    color: #777;
    font-style: italic;
}

textarea {
    min-width: 394px;
}

/* ===== Color input (нормальный квадрат цвета) ===== */
input[type="color"] {
    padding: 0;
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background: none;
}

input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
}

input[type="color"]::-webkit-color-swatch {
    border-radius: 6px;
    border: 1.5px solid #ccc;
}

input[type="color"]::-moz-color-swatch {
    border-radius: 6px;
    border: 1.5px solid #ccc;
}

.size-control {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 12px;
    width: 100%;
    max-width: 420px;

    span {
        font-weight: 600;
        white-space: nowrap;
    }
}

.size-value {
    min-width: 60px;
    text-align: right;
    font-variant-numeric: tabular-nums;
}

input[type="range"] {
    accent-color: #42b983;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
    max-width: $input-width;
}

.email-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
    max-width: $input-width;
}

.phone-form,
.sms-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
    max-width: $input-width;
}

.location-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
    max-width: $input-width;
}

.route-btn {
    padding: 10px 22px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    background-color: #42b983;
    color: white;
    transition: background-color 0.2s;

    &:hover {
        background-color: #368a6e;
    }
}

</style>
