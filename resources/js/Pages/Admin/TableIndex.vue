<template>
    <AdminLayout>
        <div class="admin-page">
            <div class="top">
                <Link href="/admin" class="back-link">← {{ t('admin.common.backToTables') }}</Link>
                <h1>{{ tableLabel(table) }}</h1>
                <div class="actions">
                    <Link :href="`/admin/${table}/create`" class="btn btn-primary">+ {{ t('admin.common.newRecord') }}</Link>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th v-for="column in columns" :key="column.name" :title="column.name">{{ column.name }}</th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="row in rows.data" :key="rowKey(row)">
                        <td v-for="column in columns" :key="column.name">{{ printValue(row[column.name]) }}</td>
                        <td class="row-actions">
                            <Link
                                v-if="primaryKey"
                                :href="`/admin/${table}/${encodeRowId(row[primaryKey])}/edit`"
                                class="btn btn-small"
                            >
                                {{ t('admin.common.edit') }}
                            </Link>
                            <button v-if="primaryKey" class="btn btn-small btn-danger" @click="removeRow(row)">
                                {{ t('admin.common.delete') }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!rows.data.length">
                        <td :colspan="columns.length + 1" class="empty">{{ t('admin.common.noRecords') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination" v-if="rows.links?.length">
                <button
                    v-for="(link, idx) in rows.links"
                    :key="idx"
                    class="page-btn"
                    :class="{ active: link.active }"
                    :disabled="!link.url"
                    @click="go(link.url)"
                    v-html="link.label"
                />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {Link, router} from '@inertiajs/vue3'
import {useI18n} from '@/Lang/useI18n'

const {t, tMaybe} = useI18n()

const props = defineProps({
    table: String,
    columns: Array,
    rows: Object,
    primaryKey: String,
    tables: {
        type: Array,
        default: () => [],
    },
})

const go = (url) => {
    if (!url) return
    router.visit(url)
}

const encodeRowId = (id) => encodeURIComponent(String(id))

const rowKey = (row) => {
    if (props.primaryKey && row[props.primaryKey] !== undefined && row[props.primaryKey] !== null) {
        return String(row[props.primaryKey])
    }
    return JSON.stringify(row)
}

const printValue = (value) => {
    if (value === null || value === undefined || value === '') return '—'
    if (typeof value === 'object') return JSON.stringify(value)
    return String(value)
}

const removeRow = (row) => {
    if (!props.primaryKey) return
    const id = row[props.primaryKey]
    if (id === undefined || id === null) return
    if (!confirm(t('admin.common.confirmDelete'))) return
    router.delete(`/admin/${props.table}/${encodeRowId(id)}`)
}

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
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.2rem 1rem 3rem;
}

.top {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 12px;
    align-items: center;
    margin-bottom: 1rem;
}

.top h1 {
    margin: 0;
    text-align: center;
    font-size: 2rem;
    overflow-wrap: anywhere;
}

.back-link {
    text-decoration: none;
    color: #bd6592;
    font-weight: 700;
}

.actions {
    display: flex;
    justify-content: flex-end;
}

.table-wrap {
    overflow: auto;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 820px;
}

th, td {
    border-bottom: 1px solid #f1f5f9;
    padding: 10px;
    text-align: left;
    vertical-align: top;
    font-size: 0.92rem;
    min-width: 140px;
    overflow-wrap: anywhere;
    word-break: break-word;
    white-space: normal;
}

th {
    background: #f8fafc;
    font-weight: 800;
    font-size: 0.82rem;
    line-height: 1.25;
}

.row-actions {
    display: flex;
    gap: 8px;
    min-width: 170px;
}

.btn {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #334155;
    border-radius: 8px;
    padding: 8px 10px;
    text-decoration: none;
    cursor: pointer;
    font-weight: 700;
}

.btn-primary {
    background: #e095bc;
    border-color: #e095bc;
    color: #fff;
}

.btn-danger {
    color: #b91c1c;
    border-color: #fecaca;
    background: #fff5f5;
}

.btn-small {
    padding: 6px 8px;
    font-size: 0.82rem;
}

.empty {
    text-align: center;
    color: #64748b;
}

.pagination {
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
}

.page-btn {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    border-radius: 8px;
    padding: 7px 11px;
    min-width: 38px;
    cursor: pointer;
    font-weight: 700;
    line-height: 1;
}

.page-btn:hover:not(:disabled) {
    background: #fce7f3;
    border-color: #f5bfd0;
    color: #7a1f47;
}

.page-btn.active {
    background: #e095bc;
    border-color: #e095bc;
    color: #fff;
}

.page-btn:disabled {
    cursor: not-allowed;
    opacity: 0.55;
    background: #f8fafc;
}

@media (max-width: 768px) {
    .top {
        grid-template-columns: 1fr;
    }

    .actions {
        justify-content: flex-start;
    }
}
</style>
