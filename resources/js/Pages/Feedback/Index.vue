<template>
    <AppLayout>
        <div class="feedback-container">
            <h2 style="margin: 2rem 0; display: flex; justify-content: center; font-size: 1.8rem">
                {{ t('list.title') }}
            </h2>

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
                        <td>{{ item.subject }}</td>

                        <td>
                            {{ t(`categories.${item.category}`) }}
                        </td>

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

const { t } = useI18n()

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
    padding: 0 20px 30px;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 35px;
        font-weight: 600;
    }
}

.feedback-list-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    padding: 30px 25px;

    .feedback-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1rem;
        color: #2c3e50;

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tbody tr:hover {
            background-color: #f1f8f5;
        }

        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 30px 0;
        }
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        color: white;

        &.new {
            background-color: #d5c13b;
        }

        &.in_progress {
            background-color: #42b983;
        }

        &.resolved {
            background-color: #4caf50;
        }
    }

    .action-link {
        color: #42b983;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;

        &:hover {
            color: #2e8b63;
            text-decoration: underline;
        }
    }
}

/* Адаптив */
@media (max-width: 768px) {
    .feedback-list-card {
        padding: 20px 15px;

        .feedback-table th,
        .feedback-table td {
            padding: 10px 8px;
            font-size: 0.9rem;
        }
    }
}
</style>
