<template>
    <AppLayout>
        <div class="feedback-container">

            <!-- Верхняя панель -->
            <div class="page-header">
                <Link href="/contacts" class="back-link">
                    {{ t('show.backContact') }}
                </Link>

                <h2>{{ t('list.title') }}</h2>
            </div>

            <div class="feedback-list-card">
                <table class="feedback-table">
                    <thead>
                    <tr>
                        <th>{{ t('list.subject') }}</th>
                        <th>{{ t('list.category') }}</th>
                        <th>{{ t('list.status') }}</th>
                        <th>{{ t('list.createdAt') }}</th>
                        <th>{{ t('list.action') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="item in feedbacks" :key="item.id">
                        <td class="subject-cell">
                            {{ item.subject }}
                        </td>

                        <td>{{ t(`categories.${item.category}`) }}</td>

                        <td>
                            <span :class="['status-badge', item.status]">
                                {{ t(`statuses.${item.status}`) }}
                            </span>
                        </td>

                        <td>{{ formatDate(item.created_at) }}</td>

                        <td>
                            <Link
                                :href="route('feedback.show', item.id)"
                                class="action-link"
                            >
                                {{ t('list.view') }}
                            </Link>
                        </td>
                    </tr>

                    <tr v-if="feedbacks.length === 0">
                        <td colspan="5" class="no-data">
                            {{ t('list.empty') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import {Link} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {useI18n} from "@/lang/useI18n.js";

const {t} = useI18n()

defineProps({
    feedbacks: Array,
})

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}
</script>

<style scoped lang="scss">
.feedback-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 20px 40px;
    font-family: 'Segoe UI', Roboto, sans-serif;
}

/* Верхняя панель */
.page-header {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin: 2rem 0 2.5rem;

    h2 {
        font-weight: 900;
        font-size: 2.4rem;
        text-align: center;
        background: linear-gradient(135deg, #e095bc, #bd6592);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        user-select: none;
    }

    .back-link {
        align-self: flex-start;
        color: #e095bc;
        font-weight: 600;
        text-decoration: none;

        &:hover {
            color: #bd6592;
            text-decoration: underline;
        }
    }
}

/* Карточка списка */
.feedback-list-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 10px 28px rgba(225, 108, 167, 0.18);
    padding: 28px;
}

/* Таблица */
.feedback-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 1rem;
    color: #475569;

    th,
    td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    th {
        background: #f8fafc;
        font-weight: 800;
        color: #475569;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    tbody tr {
        transition: background 0.25s ease, transform 0.15s ease;

        &:hover {
            background: #fce7f3;
        }
    }

    .subject-cell {
        font-weight: 600;
        color: #7a2c55;
    }

    .no-data {
        text-align: center;
        color: #a23168;
        font-style: italic;
        padding: 32px 0;
    }
}

/* Статусы */
.status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 0.8rem;
    color: white;
    white-space: nowrap;

    &.new {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    &.in_progress {
        background: linear-gradient(135deg, #e095bc, #bd6592);
    }

    &.resolved {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }
}

/* Ссылка действия */
.action-link {
    color: #e095bc;
    font-weight: 700;
    text-decoration: none;

    &:hover {
        color: #bd6592;
        text-decoration: underline;
    }
}

/* Адаптив */
@media (max-width: 768px) {
    .feedback-list-card {
        padding: 20px;
    }

    .feedback-table th,
    .feedback-table td {
        padding: 12px;
        font-size: 0.9rem;
    }
}
</style>
