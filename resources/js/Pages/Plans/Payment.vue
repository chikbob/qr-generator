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
import { useI18n } from '@/Lang/useI18n'

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

<style lang="scss" scoped>
.payment-container {
    max-width: 520px;
    margin: 4rem auto;
    padding: 2.2rem;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 12px 30px rgba(236, 72, 153, 0.2);
    text-align: center;
    font-family: 'Segoe UI', Roboto, sans-serif;

    h2 {
        font-size: 2rem;
        font-weight: 900;
        margin-bottom: 0.8rem;
        background: linear-gradient(135deg, #e095bc, #bd6592);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
}

.plan-description {
    font-size: 1rem;
    color: #475569;
    margin-bottom: 0.6rem;
}

.plan-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #bd6592;
    margin-bottom: 1.8rem;
}

.payment-form {
    max-width: 420px;
    margin: 0 auto;
    text-align: left;
}

.form-group {
    margin-bottom: 1.2rem;
    display: flex;
    flex-direction: column;

    &.half {
        width: 48%;
        display: inline-block;
    }
}

label {
    margin-bottom: 6px;
    font-weight: 700;
    color: #bd6592;
}

input {
    padding: 10px 12px;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    font-size: 1rem;
    outline: none;
    background: #f8fafc;
    color: #475569;
    transition: all 0.25s ease;

    &:focus {
        border-color: #e095bc;
        box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.15);
    }
}

.btn-pay {
    width: 100%;
    margin-top: 1rem;
    padding: 14px 0;
    border-radius: 999px;
    border: none;
    font-weight: 800;
    font-size: 1.05rem;
    cursor: pointer;
    color: #fff;
    background: linear-gradient(135deg, #e095bc, #bd6592);
    transition: all 0.25s ease;

    &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(236, 72, 153, 0.45);
    }

    &:disabled {
        background: #fce7f3;
        cursor: not-allowed;
        box-shadow: none;
        color: #9d174d;
    }
}
</style>
