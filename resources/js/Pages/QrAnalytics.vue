<template>
    <AppLayout>
        <div class="qr-analytics">
            <h2>{{ t('qrAnalytics.title') }}</h2>

            <div class="qr-summary">
                <img :src="qrCode.image_path" class="qr-image" />
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
import { onMounted, ref } from "vue"
import { usePage } from "@inertiajs/vue3"
import { useI18n } from "@/lang/useI18n"
import Chart from "chart.js/auto"

const { t } = useI18n()
const { props } = usePage()
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

<style scoped>
.qr-analytics {
    max-width: 900px;
    margin: 2rem auto;
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    color: #2c3e50;
}

h2, h3 {
    text-align: center;
    color: #34495e;
}

.qr-summary {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    margin-bottom: 2rem;
}

.qr-image {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.chart-container {
    margin: 1rem 0 2rem;
    width: 100%;
}

.scan-table {
    width: 100%;
    border-collapse: collapse;
}

.scan-table th, .scan-table td {
    padding: 8px 12px;
    border: 1px solid #ddd;
    text-align: center;
}

.scan-table th {
    background-color: #f4f6f8;
}
</style>
