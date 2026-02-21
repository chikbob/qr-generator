<template>
    <AdminLayout>
        <div class="admin-page">
            <Link :href="`/admin/${table}`" class="back-link">← {{ t('admin.common.backToTable') }}</Link>
            <h1>
                {{ mode === 'create' ? t('admin.form.createTitle') : t('admin.form.editTitle') }}:
                {{ tableLabel(table) }}
            </h1>

            <form @submit.prevent="submit" class="form-card">
                <div v-for="column in columns" :key="column.name" class="field">
                    <label :for="column.name">
                        {{ column.name }}
                        <small v-if="!column.nullable">*</small>
                    </label>

                    <textarea
                        v-if="column.input_type === 'textarea'"
                        :id="column.name"
                        v-model="form[column.name]"
                        rows="4"
                    />

                    <input
                        v-else-if="column.input_type === 'checkbox'"
                        :id="column.name"
                        v-model="form[column.name]"
                        type="checkbox"
                    />

                    <input
                        v-else
                        :id="column.name"
                        v-model="form[column.name]"
                        :type="column.input_type === 'datetime-local' ? 'text' : column.input_type"
                        :placeholder="column.type"
                    />
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        {{ mode === 'create' ? t('admin.common.create') : t('admin.common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {Link, useForm} from '@inertiajs/vue3'
import {useI18n} from '@/Lang/useI18n'

const {t, tMaybe} = useI18n()

const props = defineProps({
    mode: String,
    table: String,
    columns: Array,
    row: Object,
    rowId: [String, Number],
    tables: {
        type: Array,
        default: () => [],
    },
})

const initialData = {}
for (const column of props.columns || []) {
    const currentValue = props.row?.[column.name]
    if (column.input_type === 'checkbox') {
        initialData[column.name] = Boolean(currentValue)
    } else {
        initialData[column.name] = currentValue ?? ''
    }
}

const form = useForm(initialData)

const submit = () => {
    if (props.mode === 'create') {
        form.post(`/admin/${props.table}`)
        return
    }

    form.patch(`/admin/${props.table}/${encodeURIComponent(String(props.rowId))}`)
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
    max-width: 760px;
    margin: 0 auto;
    padding: 1.2rem 1rem 3rem;
}

.back-link {
    text-decoration: none;
    color: #bd6592;
    font-weight: 700;
}

h1 {
    margin: 0.7rem 0 1rem;
    font-size: 1.8rem;
    overflow-wrap: anywhere;
}

.form-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

label {
    font-weight: 700;
}

small {
    color: #b91c1c;
}

input[type="text"],
input[type="number"],
textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 12px;
    font: inherit;
    box-sizing: border-box;
}

input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

.actions {
    display: flex;
    justify-content: flex-end;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 10px 14px;
    cursor: pointer;
    font-weight: 700;
}

.btn-primary {
    background: #e095bc;
    color: #fff;
}
</style>
