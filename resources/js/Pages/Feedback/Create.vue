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
    margin: 2rem auto;
    padding: 0 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    h1 {
        text-align: center;
        margin-bottom: 2rem;
        font-weight: 700;
        color: #2c3e50;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
}

.form-group {
    display: flex;
    flex-direction: column;

    label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #34495e;
    }
}

.form-input {
    padding: 10px 12px;
    border: 1.5px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    font-family: inherit;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;

    &:focus {
        outline: none;
        border-color: #42b983;
        box-shadow: 0 0 6px rgba(66, 185, 131, 0.5);
    }

    &:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
    }
}

.error-text {
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #e74c3c;
}

.btn-primary {
    align-self: center;
    background-color: #42b983;
    color: white;
    font-weight: 600;
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;

    &:hover:not(:disabled) {
        background-color: #369d6f;
    }

    &:disabled {
        background-color: #8fcab7;
        cursor: not-allowed;
    }
}
</style>
