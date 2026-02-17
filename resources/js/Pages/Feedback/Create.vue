<template>
    <AppLayout>
        <div class="form-container">
            <h1>{{ $t('create.title') }}</h1>

            <form @submit.prevent="submit" novalidate>
                <div class="form-group">
                    <label>{{ t('create.subject') }}</label>
                    <input v-model="form.subject" class="form-input"/>
                    <span v-if="form.errors.subject" class="error-text">
                        {{ form.errors.subject }}
                    </span>
                </div>

                <div class="form-group">
                    <label>{{ t('create.category') }}</label>
                    <select v-model="form.category" class="form-input">
                        <option value="general">{{ t('categories.general') }}</option>
                        <option value="bug">{{ t('categories.bug') }}</option>
                        <option value="idea">{{ t('categories.idea') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ t('create.message') }}</label>
                    <textarea v-model="form.message" rows="6" class="form-input"/>
                </div>

                <button class="btn-primary" :disabled="form.processing">
                    {{
                        form.processing
                            ? t('create.submitting')
                            : t('create.submit')
                    }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import {useForm} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {useI18n} from "@/lang/useI18n.js";

const { t } = useI18n()

const form = useForm({
    subject: '',
    category: 'general',
    message: '',
})

const submit = () => {
    form.post(route('feedback.store'))
}
</script>

<style scoped lang="scss">
.form-container {
    max-width: 600px;
    margin: 2.5rem auto;
    padding: 0 1rem;
    font-family: 'Segoe UI', Roboto, sans-serif;

    h1 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2.2rem;
        font-weight: 900;
        background: linear-gradient(135deg, #e095bc, #bd6592);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 1.6rem;
        background: #fff;
        padding: 2rem;
        border-radius: 24px;
        box-shadow: 0 8px 24px rgba(225, 108, 167, 0.18);
    }
}

.form-group {
    display: flex;
    flex-direction: column;

    label {
        margin-bottom: 0.5rem;
        font-weight: 700;
        color: #475569;
    }
}

.form-input {
    padding: 14px 16px;
    border: 1.8px solid #e2e8f0;
    border-radius: 16px;
    font-size: 1.05rem;
    background: #f8fafc;
    transition: all 0.25s ease;

    &:focus {
        outline: none;
        border-color: #e095bc;
        box-shadow: 0 0 10px rgba(225, 108, 167, 0.3);
        background: #fff;
    }
}

.error-text {
    margin-top: 0.25rem;
    font-size: 0.85rem;
    color: #e11d48;
}

.btn-primary {
    align-self: center;
    margin-top: 1rem;
    background: linear-gradient(135deg, #e095bc, #bd6592);
    color: white;
    font-weight: 700;
    padding: 14px 36px;
    border: none;
    border-radius: 24px;
    cursor: pointer;
    font-size: 1.1rem;
    box-shadow: 0 6px 18px rgba(225, 108, 167, 0.4);
    transition: all 0.3s ease;

    &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 26px rgba(189, 101, 146, 0.6);
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none;
    }
}
</style>
