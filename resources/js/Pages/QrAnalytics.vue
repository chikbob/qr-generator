<template>
    <AppLayout>
        <div class="qr-analytics">
            <h2>{{ t('qrAnalytics.title') }}</h2>

            <div class="qr-summary">
                <img :src="qrCode.image_path" class="qr-image"/>
                <div>
                    <p><strong>{{ t('qrAnalytics.content') }}:</strong> {{ qrCode.content }}</p>
                    <p><strong>{{ t('qrAnalytics.totalScans') }}:</strong> {{ qrCode.scans_count }}</p>
                    <p><strong>{{ t('qrAnalytics.createdAt') }}:</strong> {{ formatDate(qrCode.created_at) }}</p>
                </div>
            </div>

            <h3>📊 {{ t('qrAnalytics.activityChart') }}</h3>
            <div class="chart-container">
                <canvas ref="chart"></canvas>
            </div>

            <h3>📍 {{ t('qrAnalytics.scanDetails') }}</h3>
            <table class="scan-table">
                <thead>
                <tr>
                    <th>{{ t('qrAnalytics.date') }}</th>
                    <th>{{ t('qrAnalytics.country') }}</th>
                    <th>{{ t('qrAnalytics.city') }}</th>
                    <th>{{ t('qrAnalytics.browser') }}</th>
                    <th>{{ t('qrAnalytics.device') }}</th>
                    <th>{{ t('qrAnalytics.referer') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="scan in scans" :key="scan.id">
                    <td>{{ formatDate(scan.created_at) }}</td>
                    <td>{{ scan.country || '—' }}</td>
                    <td>{{ scan.city || '—' }}</td>
                    <td>{{ scan.browser || '—' }}</td>
                    <td>{{ scan.device || '—' }}</td>
                    <td>{{ scan.referer || '—' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import {onMounted, ref} from "vue"
import {usePage} from "@inertiajs/vue3"
import {useI18n} from "@/Lang/useI18n"
import Chart from "chart.js/auto"

const {t} = useI18n()
const {props} = usePage()
const qrCode = props.qrCode
const scans = props.scans

const formatDate = (d) => new Date(d).toLocaleString()

const chart = ref(null)
onMounted(() => {
    const ctx = chart.value.getContext("2d")
    const dailyCounts = {}

    scans.forEach(scan => {
        const day = new Date(scan.created_at).toLocaleDateString()
        dailyCounts[day] = (dailyCounts[day] || 0) + 1
    })

    new Chart(ctx, {
        type: "line",
        data: {
            labels: Object.keys(dailyCounts),
            datasets: [
                {
                    label: t('qrAnalytics.scansPerDay'),
                    data: Object.values(dailyCounts),
                    borderWidth: 2,
                    tension: 0.3,
                },
            ],
        },
    })
})
</script>

<style lang="scss" scoped>
$color-primary: #db2777;
$color-primary-dark: #9d174d;
$color-primary-light: #fce7f3;
$color-secondary: #8b5cf6;
$color-bg: #fff;
$color-text: #2c3e50;
$color-text-muted: #5b2c52;
$font-main: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

.qr-analytics {
    max-width: 900px;
    margin: 3rem auto;
    background: $color-bg;
    padding: 3rem 3rem 4rem;
    border-radius: 24px;
    box-shadow: 0 18px 48px rgba($color-primary, 0.15),
    inset 0 0 30px rgba($color-primary-light, 0.4);
    color: $color-text;
    font-family: $font-main;
    user-select: none;
}

h2, h3 {
    text-align: center;
    color: $color-primary-dark;
    font-weight: 900;
    margin-bottom: 2rem;
    user-select: none;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.qr-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 2.5rem;
    align-items: center;
    justify-content: center;
    margin-bottom: 3rem;
}

.qr-image {
    width: 140px;
    height: 140px;
    border-radius: 24px;
    border: 3px solid $color-primary;
    box-shadow: 0 0 25px rgba($color-primary, 0.5),
    inset 0 0 15px rgba($color-primary-light, 0.6);
    transition: transform 0.3s ease;

    &:hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba($color-primary, 0.7),
        inset 0 0 20px rgba($color-primary-light, 0.8);
    }
}

.summary-info {
    max-width: 600px;
    font-size: 1.2rem;
    color: $color-text-muted;
    font-weight: 700;
    line-height: 1.5;
    user-select: text;

    p {
        margin-bottom: 0.8rem;
    }

    p strong {
        color: $color-primary;
        user-select: none;
    }
}

.chart-container {
    width: 100%;
    margin-bottom: 3.5rem;
    user-select: none;
    border-radius: 24px;
    padding: 1rem;
    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
    box-shadow: inset 0 0 20px rgba($color-primary-light, 0.5),
    0 6px 18px rgba($color-primary, 0.15);
}

.scan-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 2px 0;
    font-size: 1.05rem;
    color: $color-text-muted;
    user-select: text;
    border-radius: 24px;
    overflow: hidden;

    th,
    td {
        padding: 14px 18px;
        text-align: center;
        user-select: text;
    }

    th {
        background-color: $color-primary-light;
        color: $color-primary-dark;
        font-weight: 700;
        border-top-left-radius: 24px;
        border-top-right-radius: 24px;
        user-select: none;
        box-shadow: inset 0 -2px 6px rgba($color-primary, 0.15);
    }

    tbody tr {
        background: #fff0f6;
        border-radius: 20px;
        box-shadow: 0 5px 14px rgba($color-primary, 0.12);
        transition: background-color 0.3s ease;

        &:hover {
            background-color: #f9d6e4;
        }
    }
}

@media (max-width: 650px) {
    .qr-summary {
        flex-direction: column;
    }

    .summary-info {
        max-width: 100%;
        text-align: center;
    }

    .scan-table th,
    .scan-table td {
        padding: 10px 8px;
        font-size: 0.9rem;
    }
}
</style>
