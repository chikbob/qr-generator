<template>
    <AppLayout>
        <div class="payment-container">
            <h2 style="margin: 0; font-size: 1.8rem">{{ t('payment.title') }}: {{ plan.name }}</h2>
            <p class="plan-description">{{ plan.description }}</p>
            <p class="plan-price">{{ t('payment.price') }}: {{ plan.price }} USD</p>

            <form @submit.prevent="submitPayment" class="payment-form">
                <div class="form-group">
                    <label for="cardNumber">{{ t('payment.cardNumber') }}</label>
                    <input
                        id="cardNumber"
                        v-model="cardNumber"
                        type="text"
                        inputmode="numeric"
                        maxlength="19"
                        pattern="[0-9\s]{19}"
                        required
                        :placeholder="t('payment.cardNumberPlaceholder')"
                        @input="formatCardNumber"
                    />
                </div>

                <div class="form-group half">
                    <label for="expiryDate">{{ t('payment.expiryDate') }}</label>
                    <input
                        id="expiryDate"
                        v-model="expiryDate"
                        type="text"
                        inputmode="numeric"
                        maxlength="5"
                        pattern="(0[1-9]|1[0-2])\/\d{2}"
                        required
                        :placeholder="t('payment.expiryDatePlaceholder')"
                        @input="formatExpiryDate"
                    />
                </div>

                <div class="form-group half">
                    <label for="cvv">{{ t('payment.cvv') }}</label>
                    <input
                        id="cvv"
                        v-model="cvv"
                        type="password"
                        inputmode="numeric"
                        maxlength="3"
                        pattern="\d{3}"
                        required
                        :placeholder="t('payment.cvvPlaceholder')"
                        @input="formatCVV"
                    />
                </div>

                <button type="submit" :disabled="processing" class="btn-pay">
                    {{ processing ? t('payment.processing') : t('payment.pay') }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useI18n } from '@/lang/useI18n'

const { t } = useI18n()

const { props } = usePage()
const plan = props.plan

const cardNumber = ref('')
const expiryDate = ref('')
const cvv = ref('')
const processing = ref(false)

function formatCardNumber(e) {
    let value = e.target.value.replace(/\D/g, '').slice(0, 16)
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ')
    cardNumber.value = value
}

function formatExpiryDate(e) {
    let value = e.target.value.replace(/\D/g, '').slice(0, 4)
    if (value.length > 2) value = value.slice(0, 2) + '/' + value.slice(2)
    expiryDate.value = value
}

function formatCVV(e) {
    cvv.value = e.target.value.replace(/\D/g, '').slice(0, 3)
}

function submitPayment() {
    processing.value = true
    router.post(
        route('plans.pay', { plan: plan.id }),
        {
            card_number: cardNumber.value,
            expiry_date: expiryDate.value,
            cvv: cvv.value,
        },
        {
            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>

<style scoped>
/* Стили оставляем без изменений */
.payment-container {
    max-width: 500px;
    margin: 60px auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    color: #2c3e50;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.plan-description {
    margin-bottom: 0.8rem;
    color: #34495e;
}

.plan-price {
    margin-bottom: 1.8rem;
    font-weight: 600;
    color: #42b983;
}

.payment-form {
    max-width: 400px;
    margin: 0 auto;
    text-align: left;
}

.form-group {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.form-group.half {
    width: 48%;
    display: inline-block;
}

label {
    margin-bottom: 6px;
    font-weight: 600;
    color: #34495e;
}

input[type='text'],
input[type='password'] {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    outline: none;
    transition: border-color 0.3s ease;
    width: 100%;
}

input:focus {
    border-color: #42b983;
}

.btn-pay {
    width: 100%;
    padding: 12px 0;
    background-color: #42b983;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-pay:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.btn-pay:hover:not(:disabled) {
    background-color: #369d6e;
}
</style>
