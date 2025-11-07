<template>
    <AppLayout>
        <div class="show-container">
            <Link href="/feedback" class="back-link">← Назад до списку</Link>

            <h1 class="title">{{ feedback.subject }}</h1>

            <div class="feedback-details">
                <p><strong>Категорія:</strong> {{ categoryLabel(feedback.category) }}</p>
                <p><strong>Статус:</strong> <span :class="['status', feedback.status]">{{
                        statusLabel(feedback.status)
                    }}</span></p>
                <p><strong>Тариф:</strong> {{ priorityLabel(feedback.priority) }}</p>
                <p><strong>Дата створення:</strong> {{ formatDate(feedback.created_at) }}</p>

                <hr/>

                <p class="message-content" v-html="formattedMessage"></p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {Link} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {computed} from "vue";

const props = defineProps({
    feedback: Object,
})

const categoryLabel = (cat) => {
    switch (cat) {
        case 'general':
            return 'Загальне питання'
        case 'bug':
            return 'Помилка'
        case 'idea':
            return 'Ідея / Пропозиція'
        default:
            return cat
    }
}

const statusLabel = (status) => {
    switch (status) {
        case 'new':
            return 'В очікуванні'
        case 'in_progress':
            return 'В роботі'
        case 'resolved':
            return 'Вирішено'
        default:
            return status
    }
}

const priorityLabel = (priority) => {
    switch (priority) {
        case 'high':
            return 'Enterprice'
        case 'medium':
            return 'Pro'
        case 'low':
            return 'Free'
        default:
            return priority
    }
}

const formatDate = (dateStr) => {
    const d = new Date(dateStr)
    return d.toLocaleString('uk-UA', {dateStyle: 'long', timeStyle: 'short'})
}

// Чтобы корректно отображать переносы строк, заменим \n на <br>
const formattedMessage = computed(() =>
    props.feedback.message
        ? props.feedback.message.replace(/\n/g, '<br />')
        : ''
)
</script>

<style scoped lang="scss">
.show-container {
    max-width: 700px;
    margin: 3rem auto;
    padding: 0 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    .back-link {
        color: #42b983;
        text-decoration: none;
        font-weight: 600;

        &:hover {
            text-decoration: underline;
        }
    }

    .title {
        margin-top: 1rem;
        margin-bottom: 1.5rem;
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .feedback-details {
        background-color: #fff;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgb(0 0 0 / 0.08);
        color: #34495e;

        p {
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }

        hr {
            margin: 1.5rem 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        .status {
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 12px;
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

        .message-content {
            white-space: pre-wrap;
            line-height: 1.5;
            font-size: 1.1rem;
            color: #2d3436;
        }
    }
}
</style>
