<template>
    <AppLayout>
        <div class="show-container">
            <Link href="/feedback" class="back-link">
                {{ $t('show.back') }}
            </Link>

            <h1 class="title">{{ feedback.subject }}</h1>

            <div class="feedback-details">
                <p>
                    <strong>{{ $t('show.category') }}:</strong>
                    {{ $t(`categories.${feedback.category}`) }}
                </p>

                <p>
                    <strong>{{ $t('show.status') }}:</strong>
                    <span :class="['status', feedback.status]">
                        {{ $t(`statuses.${feedback.status}`) }}
                    </span>
                </p>

                <p>
                    <strong>{{ $t('show.plan') }}:</strong>
                    {{ $t(`priorities.${feedback.priority}`) }}
                </p>

                <p>
                    <strong>{{ $t('show.createdAt') }}:</strong>
                    {{ formatDate(feedback.created_at) }}
                </p>

                <hr/>

                <p class="message-content" v-html="formattedMessage"/>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {Link} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {computed} from 'vue'

const props = defineProps({
    feedback: Object,
})

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString(undefined, {
        dateStyle: 'long',
        timeStyle: 'short',
    })
}

const formattedMessage = computed(() =>
    props.feedback.message
        ? props.feedback.message.replace(/\n/g, '<br />')
        : ''
)
</script>

<style scoped lang="scss">
.show-container {
    max-width: 720px;
    margin: 3rem auto;
    padding: 0 1rem;
    font-family: 'Segoe UI', Roboto, sans-serif;

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #e095bc;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1rem;

        &:hover {
            color: #bd6592;
            text-decoration: underline;
        }
    }

    .title {
        margin: 0.5rem 0 2rem;
        font-size: 2.4rem;
        font-weight: 900;
        line-height: 1.2;
        background: linear-gradient(135deg, #e095bc, #bd6592);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        user-select: none;
    }

    .feedback-details {
        background: #fff;
        padding: 2.2rem;
        border-radius: 24px;
        box-shadow: 0 10px 28px rgba(225, 108, 167, 0.18);
        color: #475569;

        p {
            margin: 0.7rem 0;
            font-size: 1.05rem;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;

            strong {
                font-weight: 700;
                color: #7a2c55;
            }
        }

        hr {
            margin: 2rem 0;
            border: none;
            border-top: 1px solid #e2e8f0;
        }

        .status {
            padding: 4px 14px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.8rem;
            color: #fff;
            letter-spacing: 0.03em;

            &.new {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            &.in_progress {
                background: linear-gradient(135deg, #e095bc, #bd6592);
                box-shadow: 0 0 12px rgba(225, 108, 167, 0.35);
            }

            &.resolved {
                background: linear-gradient(135deg, #22c55e, #16a34a);
            }
        }

        .message-content {
            margin-top: 1.5rem;
            padding: 1.4rem 1.6rem;
            background: #f8fafc;
            border-left: 5px solid #e095bc;
            border-radius: 16px;
            line-height: 1.7;
            font-size: 1.05rem;
            color: #2d3436;
            box-shadow: inset 0 0 0 1px #e2e8f0;
            word-break: break-word;
        }
    }
}

/* Адаптив */
@media (max-width: 640px) {
    .show-container {
        margin: 2rem auto;

        .title {
            font-size: 2rem;
        }

        .feedback-details {
            padding: 1.6rem;
        }
    }
}
</style>
