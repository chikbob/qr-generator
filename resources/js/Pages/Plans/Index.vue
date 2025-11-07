<template>
    <AppLayout>
        <div class="plans-container">
            <h2 style="margin: 2rem 0; font-size: 1.8rem">Вибір тарифу</h2>

            <div v-if="flash.success" class="success-message">{{ flash.success }}</div>
            <div v-if="flash.error" class="error-message">{{ flash.error }}</div>

            <div v-if="plans.length === 0" class="empty-plans">
                Тарифи відсутні.
            </div>

            <div v-else class="plans-grid">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    :class="['plan-card', { active: plan.id === currentPlanId }]"
                >
                    <h2 class="plan-name">
                        {{ plan.name }}
                        <span v-if="plan.id === currentPlanId" class="current-tag">(Поточний)</span>
                    </h2>
                    <p class="plan-description">{{ plan.description }}</p>
                    <p class="plan-price">Ціна: {{ plan.price }} USD</p>

                    <button
                        :disabled="plan.id === currentPlanId"
                        @click="handlePlanSelection(plan)"
                        class="btn-select"
                    >
                        {{ plan.id === currentPlanId ? 'Вибрано' : 'Вибрати' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const plans = computed(() => page.props.plans || [])
const flash = computed(() => page.props.flash || {})
const currentPlanId = computed(() => page.props.currentPlanId)

/**
 * Обробка вибору тарифу
 */
function handlePlanSelection(plan) {
    // Безкоштовний тариф — підтвердження переходу
    if (plan.name.toLowerCase() === 'free' || plan.price == 0) {
        if (confirm('Ви дійсно хочете скасувати підписку та перейти на Free-тариф?')) {
            router.post(route('plans.subscribe'), { plan_id: plan.id }, {
                onSuccess: () => {
                    // Оновлюємо дані користувача без повного перезавантаження
                    router.reload({ only: ['auth', 'currentPlanId', 'flash'] })
                }
            })
        }
    } else {
        // Перехід на сторінку оплати
        router.get(route('plans.payment', { plan: plan.id }))
    }
}
</script>

<style scoped>
.plans-container {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.success-message {
    color: #2e7d32;
    background: #e8f5e9;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.error-message {
    color: #c62828;
    background: #ffebee;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.plan-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: 0.2s;
}

.plan-card.active {
    border-color: #42b983;
    box-shadow: 0 4px 12px rgba(66, 185, 131, 0.3);
}

.plan-card:hover {
    transform: translateY(-3px);
}

.plan-name {
    margin-bottom: 10px;
}

.current-tag {
    color: #42b983;
    font-size: 0.9em;
    margin-left: 4px;
}

.btn-select {
    margin-top: 10px;
    background: #42b983;
    border: none;
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-select:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
