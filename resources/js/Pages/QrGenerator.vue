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
                    style="width: 441px;"
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
                            :disabled="!canUseDynamic || !dynamicCompatible"
                        />
                        {{ t('qrGenerator.dynamic') }}
                    </label>

                    <p v-if="!canUseDynamic" class="pro-hint">
                        {{ t('qrGenerator.dynamicProHint') }}
                    </p>
                    <p v-else-if="hasContent && !dynamicCompatible" class="pro-hint">
                        {{ t('qrGenerator.dynamicUnsupported') }}
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
import { ref, computed, watch } from 'vue'
import QRCode from 'qrcode'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useI18n } from '@/Lang/useI18n'

const { t } = useI18n()
const page = usePage()

const qrCanvas = ref(null)

const qrType = ref('text')
const size = ref(200)

const colorDark = ref('#000000')
const colorLight = ref('#ffffff')

const isDynamic = ref(false)

// Получаем plan_id (по умолчанию 1 = Free)
const userPlanId = computed(() => page.props.auth?.user?.plan_id ?? 1)

// Разрешаем dynamic для plan_id 2 и 3
const canUseDynamic = computed(() => [2, 3].includes(Number(userPlanId.value)))
const dynamicCompatible = computed(() => {
    if (!qrContent.value) return false
    const match = qrContent.value.trim().match(/^([a-z][a-z0-9+.-]*):/i)
    if (!match) return false
    const scheme = match[1].toLowerCase()
    return ['http', 'https', 'mailto', 'tel', 'sms', 'geo'].includes(scheme)
})
const hasContent = computed(() => Boolean(qrContent.value))

const pdfFile = ref(null)
const pdfFileName = ref('')
const pdfFileUrl = ref('')

const uploadPdfFile = async (file) => {
    const formData = new FormData()
    formData.append('file', file)

    const response = await fetch('/api/upload-pdf', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })

    if (!response.ok) {
        const errorText = await response.text()
        throw new Error(errorText || 'Ошибка загрузки')
    }

    const data = await response.json()
    return data.url
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
    wifi: { ssid: '', password: '', encryption: 'WPA' },
    contact: { name: '', phone: '', email: '', company: '', website: '', address: '' },
    email: { to: '', subject: '', body: '' },
    phone: { number: '' },
    sms: { number: '', message: '' },
    location: { address: '', mapProvider: '' },
    pdf: { document: '' }
})

/* ✅ QR появляется только если есть данные */
const qrContent = computed(() => {
    switch (qrType.value) {
        case 'wifi': {
            const { ssid, password, encryption } = qrData.value.wifi
            if (!ssid && !password) return ''
            return `WIFI:T:${encryption};S:${ssid};P:${password};;`
        }
        case 'contact': {
            const c = qrData.value.contact
            if (!Object.values(c).some(v => v?.trim())) return ''
            return [
                'BEGIN:VCARD',
                'VERSION:3.0',
                c.name && `FN:${c.name}`,
                c.company && `ORG:${c.company}`,
                c.phone && `TEL:${c.phone}`,
                c.email && `EMAIL:${c.email}`,
                c.website && `URL:${c.website}`,
                c.address && `ADR:;;${c.address}`,
                'END:VCARD'
            ].filter(Boolean).join('\n')
        }
        case 'email': {
            const { to, subject, body } = qrData.value.email
            if (!to) return ''
            const params = new URLSearchParams()
            if (subject) params.append('subject', subject)
            if (body) params.append('body', body)
            return `mailto:${to}${params.toString() ? '?' + params.toString() : ''}`
        }
        case 'phone':
            return qrData.value.phone.number ? `tel:${qrData.value.phone.number}` : ''
        case 'sms': {
            const { number, message } = qrData.value.sms
            if (!number) return ''
            return `sms:${number}?body=${encodeURIComponent(message)}`
        }
        case 'location': {
            const address = qrData.value.location.address.trim()
            if (!address) return ''
            const encoded = encodeURIComponent(address)
            return qrData.value.location.mapProvider === 'yandex'
                ? `https://yandex.ru/maps/?text=${encoded}`
                : `https://www.google.com/maps/search/?api=1&query=${encoded}`
        }
        case 'pdf':
            return pdfFileUrl.value || ''
        case 'text': {
            const text = qrData.value.text.trim()
            return text || ''
        }
        default:
            return ''
    }
})

const generateQR = async () => {
    if (!qrCanvas.value || !qrContent.value) return
    const CANVAS_SIZE = 260
    await QRCode.toCanvas(qrCanvas.value, qrContent.value, {
        width: CANVAS_SIZE,
        scale: size.value / 100,
        color: { dark: colorDark.value, light: colorLight.value }
    })
}

watch([qrContent, size, colorDark, colorLight], generateQR)

const saveToHistory = async () => {
    if (!qrContent.value) return alert('Нет данных для генерации QR')
    if (canUseDynamic.value && isDynamic.value && !dynamicCompatible.value) {
        return alert(t('qrGenerator.dynamicUnsupported'))
    }

    const payloadData = qrType.value === 'text'
        ? { text: qrData.value.text }
        : qrData.value[qrType.value] ?? null

    try {
        await router.post('/qr', {
            type: qrType.value,
            content: qrContent.value,
            payload: payloadData,
            size: size.value,
            color_dark: colorDark.value,
            color_light: colorLight.value,
            is_dynamic: canUseDynamic.value ? isDynamic.value : false
        })
    } catch (error) {
        console.error('Ошибка сохранения QR:', error)
        alert('Ошибка при сохранении QR. Проверьте консоль.')
    }
}

// Сбрасываем данные при смене типа QR
watch(qrType, () => {
    Object.keys(qrData.value).forEach(key => {
        if (typeof qrData.value[key] === 'string') qrData.value[key] = ''
        else Object.keys(qrData.value[key]).forEach(k => qrData.value[key][k] = '')
    })

    if (qrType.value !== 'pdf') {
        pdfFile.value = null
        pdfFileName.value = ''
        if (pdfFileUrl.value) {
            URL.revokeObjectURL(pdfFileUrl.value)
            pdfFileUrl.value = ''
        }
    }
})

watch(dynamicCompatible, (value) => {
    if (!value) isDynamic.value = false
})
</script>

<style scoped lang="scss">
$accent: #e095bc;
$accent-dark: #bd6592;
$accent-soft: #fce7f3;
$text-main: #0f172a;
$text-secondary: #475569;
$border: #e2e8f0;
$bg-soft: #f8fafc;

.qr-generator {
    max-width: 820px;
    margin: 3rem auto;
    padding: 3rem 2rem;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(15, 23, 42, .08);
    color: $text-main;
    text-align: center;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;

    h1 {
        margin-bottom: 2rem;
        font-size: 2.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, $accent, $accent-dark);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
}

.input-container {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
}

input, textarea {
    min-width: 381px;
}

select {
    min-width: 480px;
}

textarea,
input,
select {
    padding: 14px 18px;
    border-radius: 16px;
    border: 1.5px solid $border;
    font-size: .95rem;
    font-family: inherit;
    color: $text-main;
    background: #fff;
    transition: all .25s ease;

    &::placeholder {
        color: #94a3b8;
    }

    &:focus {
        outline: none;
        border-color: $accent;
        box-shadow: 0 0 0 4px rgba(224, 149, 188, .25);
        transform: translateY(-1px);
    }
}

select {
    appearance: none;
    background: #fff url("data:image/svg+xml,%3Csvg ... %3E") no-repeat right 16px center / 16px;
}

textarea {
    resize: vertical;
    min-height: 110px;
}

.wifi-form, .contact-form, .email-form, .phone-form, .sms-form, .location-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
    width: 100%;
    max-width: 480px;
}

.qr-container {
    margin-top: 3rem;
    padding: 2.5rem;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(15, 23, 42, .08);
}

.qr-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;

    canvas {
        padding: 16px;
        border-radius: 20px;
        box-shadow: 0 20px 45px rgba(224, 149, 188, .35);
        transition: transform .3s ease;

        &:hover {
            transform: scale(1.03);
        }
    }
}

.controls {
    display: flex;
    flex-direction: column;
    gap: 1.6rem;
    align-items: center;
}

.size-control {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 14px;
    width: 100%;
    align-items: center;

    span {
        font-weight: 600;
        font-size: .9rem;
    }
}

.size-value {
    min-width: 64px;
    text-align: right;
    font-weight: 700;
    color: $accent-dark;
}

input[type="range"] {
    accent-color: $accent;
    width: 100%;
    min-width: 0;
}

.color-controls {
    display: flex;
    gap: 1.4rem;

    label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .9rem;
        font-weight: 600;
    }
}

input[type="color"] {
    -webkit-appearance: none;
    appearance: none;
    width: 42px;
    height: 42px;
    padding: 0;
    min-width: 42px;
    border-radius: 12px;
    border: 1.5px solid $border;
    cursor: pointer;
}

.checkbox {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
    width: 100%;
    max-width: 480px;
    font-size: .9rem;
    font-weight: 500;
    color: $text-secondary;

    input {
        margin: 0;
        transform: scale(1.15);
        accent-color: $accent;
    }
}

.pro-hint {
    font-size: .85rem;
    color: #94a3b8;
}

.action-buttons {
    margin-top: 2.5rem;
}

.btn.save {
    background: linear-gradient(135deg, $accent, $accent-dark);
    padding: 15px 38px;
    border-radius: 999px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    color: #fff;
    cursor: pointer;
    box-shadow: 0 14px 32px rgba(224, 149, 188, .45);
    transition: all .3s ease;

    &:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 20px 50px rgba(224, 149, 188, .6);
    }
}

.placeholder {
    margin-top: 4rem;
    color: $accent-dark;
    font-style: italic;
    font-weight: 600;
}

@media (max-width: 768px) {
    .qr-generator {
        padding: 2rem 1.2rem;
    }
    .color-controls {
        flex-direction: column;
        align-items: center;
    }
    .size-control {
        grid-template-columns: 1fr;
        gap: 8px;
        text-align: center;
    }
}
</style>
