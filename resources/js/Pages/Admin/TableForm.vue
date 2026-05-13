<template>
    <AdminLayout>
        <div class="admin-page">
            <div class="hero">
                <Link :href="`/admin/${table}`" class="back-link">← {{ t('admin.common.backToTable') }}</Link>
                <div class="hero-copy">
                    <span class="eyebrow">{{ modeLabel }}</span>
                    <h1>{{ tableLabel(table) }}</h1>
                    <p>{{ modeDescription }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="form-shell">
                <section class="form-card">
                    <div class="card-head">
                        <div>
                            <h2>Поля запису</h2>
                            <p>Типи та обов'язковість полів беруться зі структури таблиці.</p>
                        </div>
                        <div class="meta-chip">
                            <span>{{ columns.length }}</span>
                            <small>полів</small>
                        </div>
                    </div>

                    <div class="fields-grid">
                        <div
                            v-for="column in columns"
                            :key="column.name"
                            class="field"
                            :class="{
                                'field-wide': column.input_type === 'textarea',
                                'field-checkbox': column.input_type === 'checkbox',
                            }"
                        >
                            <label :for="column.name" class="field-label">
                                <span>{{ column.name }}</span>
                                <span class="field-badges">
                                    <small v-if="!column.nullable" class="required-badge">required</small>
                                    <small class="type-badge">{{ column.type }}</small>
                                </span>
                            </label>

                            <textarea
                                v-if="column.input_type === 'textarea'"
                                :id="column.name"
                                v-model="form[column.name]"
                                rows="5"
                                class="field-control"
                                :placeholder="column.type"
                            />

                            <label v-else-if="column.input_type === 'checkbox'" class="checkbox-row">
                                <input
                                    :id="column.name"
                                    v-model="form[column.name]"
                                    type="checkbox"
                                />
                                <span>
                                    {{ form[column.name] ? 'Увімкнено' : 'Вимкнено' }}
                                </span>
                            </label>

                            <input
                                v-else
                                :id="column.name"
                                v-model="form[column.name]"
                                :type="column.input_type === 'datetime-local' ? 'text' : column.input_type"
                                class="field-control"
                                :placeholder="column.type"
                            />

                            <p v-if="form.errors[column.name]" class="field-error">
                                {{ form.errors[column.name] }}
                            </p>
                        </div>
                    </div>
                </section>

                <aside class="side-card">
                    <div class="side-block">
                        <h3>Таблиця</h3>
                        <strong>{{ table }}</strong>
                    </div>
                    <div class="side-block">
                        <h3>Режим</h3>
                        <strong>{{ modeLabel }}</strong>
                    </div>
                    <div class="side-block" v-if="mode === 'edit' && rowId !== undefined && rowId !== null">
                        <h3>ID запису</h3>
                        <strong>{{ rowId }}</strong>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Збереження...' : submitLabel }}
                        </button>
                        <Link :href="`/admin/${table}`" class="btn btn-secondary">Скасувати</Link>
                    </div>
                </aside>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {computed} from 'vue'
import {Link, useForm} from '@inertiajs/vue3'
import {useI18n} from '@/Lang/useI18n'

const {t, tMaybe} = useI18n()

const props = defineProps({
    mode: String,
    table: String,
    columns: {
        type: Array,
        default: () => [],
    },
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

const modeLabel = computed(() => (
    props.mode === 'create' ? 'Нова запись' : 'Редактирование'
))

const modeDescription = computed(() => (
    props.mode === 'create'
        ? 'Заполните поля и создайте новую запись в выбранной таблице.'
        : 'Изменения сохранятся сразу в таблицу после отправки формы.'
))

const submitLabel = computed(() => (
    props.mode === 'create' ? t('admin.common.create') : t('admin.common.save')
))

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
    max-width: 1240px;
    margin: 0 auto;
    padding: 1.5rem 1rem 3rem;
}

.hero {
    display: grid;
    gap: 16px;
    margin-bottom: 1.2rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: fit-content;
    text-decoration: none;
    color: #9f1239;
    font-weight: 800;
}

.hero-copy {
    padding: 24px 26px;
    border: 1px solid #f5bfd0;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(255, 247, 251, 0.98), rgba(255, 237, 245, 0.98));
    box-shadow: 0 18px 40px rgba(189, 101, 146, 0.12);
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: #fce7f3;
    color: #9f1239;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
}

.hero-copy h1 {
    margin: 12px 0 8px;
    font-size: 2rem;
    color: #4c0519;
    overflow-wrap: anywhere;
}

.hero-copy p {
    margin: 0;
    max-width: 760px;
    color: #7a1f47;
    font-weight: 600;
    line-height: 1.5;
}

.form-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 280px;
    gap: 18px;
    align-items: start;
}

.form-card,
.side-card {
    border: 1px solid #f1d2de;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 18px 44px rgba(148, 163, 184, 0.12);
}

.form-card {
    padding: 24px;
}

.card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
}

.card-head h2 {
    margin: 0 0 6px;
    font-size: 1.15rem;
    color: #4c0519;
}

.card-head p {
    margin: 0;
    color: #7a1f47;
    font-size: 0.95rem;
}

.meta-chip {
    min-width: 84px;
    padding: 10px 12px;
    border-radius: 16px;
    text-align: center;
    background: linear-gradient(135deg, #fdf2f8, #ffe4eb);
    color: #9f1239;
}

.meta-chip span {
    display: block;
    font-size: 1.35rem;
    font-weight: 900;
}

.meta-chip small {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.fields-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field-wide {
    grid-column: 1 / -1;
}

.field-checkbox {
    justify-content: center;
}

.field-label {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    color: #4c0519;
    font-weight: 800;
}

.field-badges {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 6px;
}

.required-badge,
.type-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0;
}

.required-badge {
    background: #ffe4e6;
    color: #be123c;
}

.type-badge {
    background: #f8fafc;
    color: #475569;
}

.field-control {
    width: 100%;
    min-height: 46px;
    border: 1px solid #f1d2de;
    border-radius: 14px;
    padding: 12px 14px;
    font: inherit;
    color: #334155;
    background: #fff;
    box-sizing: border-box;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.field-control:focus {
    outline: none;
    border-color: #e095bc;
    box-shadow: 0 0 0 4px rgba(224, 149, 188, 0.16);
    background: #fffdfd;
}

textarea.field-control {
    resize: vertical;
    min-height: 128px;
}

.checkbox-row {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-height: 46px;
    padding: 12px 14px;
    border: 1px solid #f1d2de;
    border-radius: 14px;
    background: #fff;
    color: #475569;
    font-weight: 700;
}

.checkbox-row input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #db2777;
}

.field-error {
    margin: 0;
    color: #be123c;
    font-size: 0.82rem;
    font-weight: 700;
}

.side-card {
    position: sticky;
    top: 92px;
    padding: 18px;
    display: grid;
    gap: 16px;
}

.side-block {
    padding: 14px;
    border-radius: 16px;
    background: linear-gradient(135deg, #fff7fb, #fff1f5);
    border: 1px solid #f8d6e1;
}

.side-block h3 {
    margin: 0 0 6px;
    font-size: 0.82rem;
    text-transform: uppercase;
    color: #9f1239;
}

.side-block strong {
    color: #4c0519;
    overflow-wrap: anywhere;
}

.actions {
    display: grid;
    gap: 10px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    border-radius: 12px;
    padding: 10px 14px;
    border: 1px solid transparent;
    text-decoration: none;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
    transform: none;
}

.btn-primary {
    background: linear-gradient(135deg, #e095bc, #bd6592);
    color: #fff;
    box-shadow: 0 12px 28px rgba(189, 101, 146, 0.24);
}

.btn-secondary {
    border-color: #f5bfd0;
    background: #fff;
    color: #7a1f47;
}

@media (max-width: 980px) {
    .form-shell {
        grid-template-columns: 1fr;
    }

    .side-card {
        position: static;
    }
}

@media (max-width: 720px) {
    .hero-copy,
    .form-card,
    .side-card {
        padding: 18px;
    }

    .fields-grid {
        grid-template-columns: 1fr;
    }

    .card-head,
    .field-label {
        flex-direction: column;
    }

    .field-badges {
        justify-content: flex-start;
    }
}
</style>
