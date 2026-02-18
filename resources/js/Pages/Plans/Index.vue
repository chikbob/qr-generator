<template>
    <AppLayout>
        <div class="plans-container">
            <h2 style="margin: 2rem 0; font-size: 1.8rem">{{ t('plans.title') }}</h2>

            <div v-if="flash.success" class="success-message">{{ tMaybe(flash.success) }}</div>
            <div v-if="flash.error" class="error-message">{{ tMaybe(flash.error) }}</div>

            <div v-if="plans.length === 0" class="empty-plans">
                {{ t('plans.noPlans') }}
            </div>

            <div v-else class="plans-grid">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    :class="['plan-card', { active: plan.id === currentPlanId }]"
                >
                    <h2 class="plan-name">
                        {{ plan.name }}
                        <span v-if="plan.id === currentPlanId" class="current-tag">({{ t('plans.current') }})</span>
                    </h2>
                    <p class="plan-description">{{ plan.description }}</p>
                    <p class="plan-price">{{ t('plans.price') }}: {{ plan.price }} USD</p>

                    <button
                        :disabled="plan.id === currentPlanId"
                        @click="handlePlanSelection(plan)"
                        class="btn-select"
                    >
                        {{ plan.id === currentPlanId ? t('plans.selected') : t('plans.select') }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {router, usePage} from '@inertiajs/vue3'
import {computed} from 'vue'
import {useI18n} from '@/Lang/useI18n'

const {t, tMaybe} = useI18n()

const page = usePage()
const plans = computed(() => {
    const list = page.props.plans || []
    const seen = new Set()
    return list.filter((plan) => {
        const key = (plan.name || '').toLowerCase()
        if (seen.has(key)) return false
        seen.add(key)
        return true
    })
})
const flash = computed(() => page.props.flash || {})
const currentPlanId = computed(() => page.props.currentPlanId)

/**
 * Обработка выбора тарифа
 */
function handlePlanSelection(plan) {
    // Бесплатный тариф — подтверждение перехода
    if (plan.name.toLowerCase() === 'free' || plan.price == 0) {
        if (confirm(t('plans.confirmFree'))) {
            router.post(route('plans.subscribe'), {plan_id: plan.id}, {
                onSuccess: () => {
                    router.reload({only: ['auth', 'currentPlanId', 'flash']})
                }
            })
        }
    } else {
        router.get(route('plans.payment', {plan: plan.id}))
    }
}
</script>

<style lang="scss" scoped>
.plans-container {
    max-width: 960px;
    margin: 3rem auto;
    padding: 0 1rem;
    text-align: center;
    font-family: 'Segoe UI', Roboto, sans-serif;

    h2 {
        font-size: 2.2rem;
        font-weight: 900;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #e095bc, #bd6592);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
}

.success-message {
    background: #fce7f3;
    color: #9d174d;
    padding: 12px;
    border-radius: 12px;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.error-message {
    background: #ffe4e6;
    color: #be123c;
    padding: 12px;
    border-radius: 12px;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.empty-plans {
    font-size: 1.1rem;
    color: #475569;
}

.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
}

.plan-card {
    background: #fff;
    padding: 1.8rem;
    border-radius: 24px;
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.15);
    border: 2px solid transparent;
    transition: all 0.25s ease;
    text-align: left;

    &:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 30px rgba(236, 72, 153, 0.25);
    }

    &.active {
        border-color: #bd6592;
        background: linear-gradient(180deg, #fff, #fce7f3);
    }
}

.plan-name {
    position: relative;
    padding-right: 80px;

    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: 0.6rem;
    color: #bd6592;
    line-height: 1.3;
    flex-wrap: wrap;
}

.current-tag {
    position: absolute;
    top: 0.16em;
    right: 0;

    padding: 0 10px 4px;
    font-size: 0.7rem;
    font-weight: 700;
    line-height: 1;

    color: #e095bc;
    border-radius: 999px;
    white-space: nowrap;

    -webkit-text-fill-color: initial;
    background-clip: border-box;
}

.plan-description {
    font-size: 1rem;
    color: #475569;
    margin-bottom: 0.8rem;
}

.plan-price {
    font-size: 1.05rem;
    font-weight: 700;
    color: #bd6592;
    margin-bottom: 1.2rem;
}

.btn-select {
    width: 100%;
    padding: 10px 0;
    border-radius: 999px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    background: linear-gradient(135deg, #e095bc, #bd6592);
    color: #fff;
    transition: all 0.25s ease;

    &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(236, 72, 153, 0.4);
    }

    &:disabled {
        background: #fce7f3;
        cursor: not-allowed;
        box-shadow: none;
        color: #9d174d;
    }
}
</style>
