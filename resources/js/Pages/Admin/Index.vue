<template>
    <AdminLayout>
        <div class="admin-page">
            <h1>{{ t('admin.dashboard.title') }}</h1>
            <p class="subtitle">{{ t('admin.dashboard.subtitle') }}</p>

            <section class="kpi-grid">
                <article class="kpi-card">
                    <div class="kpi-label">{{ t('admin.dashboard.kpis.totalScans') }}</div>
                    <div class="kpi-value">{{ kpis.totalScans }}</div>
                </article>
                <article class="kpi-card">
                    <div class="kpi-label">{{ t('admin.dashboard.kpis.scansToday') }}</div>
                    <div class="kpi-value">{{ kpis.scansToday }}</div>
                </article>
                <article class="kpi-card">
                    <div class="kpi-label">{{ t('admin.dashboard.kpis.dynamicShare') }}</div>
                    <div class="kpi-value">{{ kpis.dynamicShare }}%</div>
                </article>
                <article class="kpi-card">
                    <div class="kpi-label">{{ t('admin.dashboard.kpis.usersToday') }}</div>
                    <div class="kpi-value">{{ kpis.usersToday }}</div>
                </article>
            </section>

            <section class="stats-grid">
                <article v-for="item in stats" :key="item.table" class="stat-card">
                    <div class="stat-label">{{ tableLabel(item.table) }}</div>
                    <div class="stat-value">{{ item.count }}</div>
                </article>
            </section>

            <section class="insights-grid">
                <article class="insight-card">
                    <h2>{{ t('admin.dashboard.topCountriesTitle') }}</h2>
                    <ul v-if="topCountries.length" class="countries-list">
                        <li v-for="item in topCountries" :key="item.country">
                            <span>{{ item.country || t('common.unknown') }}</span>
                            <strong>{{ item.scans }}</strong>
                        </li>
                    </ul>
                    <p v-else class="empty-text">{{ t('admin.common.noRecords') }}</p>
                </article>

                <article class="insight-card wide">
                    <h2>{{ t('admin.dashboard.recentScansTitle') }}</h2>
                    <div class="recent-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>{{ t('qrAnalytics.date') }}</th>
                                <th>{{ t('qrAnalytics.country') }}</th>
                                <th>{{ t('qrAnalytics.city') }}</th>
                                <th>{{ t('qrAnalytics.browser') }}</th>
                                <th>{{ t('qrAnalytics.device') }}</th>
                                <th>{{ t('qrAnalytics.content') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(scan, idx) in recentScans" :key="`${scan.created_at}-${idx}`">
                                <td>{{ formatDateTimeUtcPlus3(scan.created_at) }}</td>
                                <td>{{ scan.country || t('common.unknown') }}</td>
                                <td>{{ scan.city || t('common.unknown') }}</td>
                                <td>{{ scan.browser || '—' }}</td>
                                <td>{{ scan.device || '—' }}</td>
                                <td class="content-cell">{{ scan.content || '—' }}</td>
                            </tr>
                            <tr v-if="!recentScans.length">
                                <td colspan="6" class="empty-text">{{ t('admin.common.noRecords') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <div class="tables-grid">
                <Link
                    v-for="table in tables"
                    :key="table"
                    :href="`/admin/${table}`"
                    class="table-card"
                >
                    <strong>{{ tableLabel(table) }}</strong>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {Link} from '@inertiajs/vue3'
import {useI18n} from '@/Lang/useI18n'
import {formatDateTimeUtcPlus3} from '@/utils/datetime'

const {t, tMaybe} = useI18n()

defineProps({
    tables: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Array,
        default: () => [],
    },
    kpis: {
        type: Object,
        default: () => ({
            totalScans: 0,
            scansToday: 0,
            dynamicShare: 0,
            usersToday: 0,
        }),
    },
    topCountries: {
        type: Array,
        default: () => [],
    },
    recentScans: {
        type: Array,
        default: () => [],
    },
})

const tableLabel = (table) => {
    const localized = tMaybe(`admin.tables.${table}`)
    if (localized === `admin.tables.${table}`) {
        return table
    }
    return `${localized} (${table})`
}
</script>

<style scoped lang="scss">
.admin-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1rem 3rem;
}

h1 {
    margin: 0 0 0.5rem;
    font-size: 2.2rem;
    font-weight: 900;
    text-align: center;
}

.subtitle {
    margin: 0 0 1.5rem;
    text-align: center;
    color: #7a1f47;
    font-weight: 600;
}

.kpi-grid {
    margin-bottom: 1rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.kpi-card {
    border-radius: 14px;
    border: 1px solid #f5bfd0;
    background: linear-gradient(145deg, #fff, #ffeef5);
    padding: 14px;
}

.kpi-label {
    color: #7a1f47;
    font-size: 0.84rem;
    font-weight: 700;
}

.kpi-value {
    margin-top: 6px;
    font-size: 1.6rem;
    color: #6b213f;
    font-weight: 900;
}

.stats-grid {
    margin-bottom: 1.2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.stat-card {
    background: #fff;
    border: 1px solid #f5bfd0;
    border-radius: 14px;
    padding: 14px;
}

.stat-label {
    color: #9f1239;
    font-size: 0.9rem;
}

.stat-value {
    margin-top: 6px;
    font-size: 1.4rem;
    font-weight: 800;
    color: #6b213f;
}

.insights-grid {
    margin-bottom: 1.2rem;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 12px;
}

.insight-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px;
}

.insight-card h2 {
    margin: 0 0 10px;
    font-size: 1rem;
    color: #7a1f47;
}

.countries-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 8px;
}

.countries-list li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 10px;
    background: #f8fafc;
}

.recent-wrap {
    overflow: auto;
}

.recent-wrap table {
    width: 100%;
    border-collapse: collapse;
    min-width: 740px;
}

.recent-wrap th,
.recent-wrap td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.86rem;
    overflow-wrap: anywhere;
}

.content-cell {
    max-width: 260px;
}

.empty-text {
    color: #64748b;
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
}

.table-card {
    text-decoration: none;
    color: #334155;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 16px;
    transition: 0.2s ease;
}

.table-card:hover {
    border-color: #e095bc;
    box-shadow: 0 10px 20px rgba(224, 149, 188, 0.18);
}

@media (max-width: 920px) {
    .insights-grid {
        grid-template-columns: 1fr;
    }
}
</style>
