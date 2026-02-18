<template>
    <AppLayout>
        <div class="auth-form">
            <h2>{{ t('register.title') }}</h2>
            <form @submit.prevent="register">
                <input v-model="form.name" type="text" :placeholder="t('register.namePlaceholder')" required/>
                <input v-model="form.email" type="email" :placeholder="t('register.emailPlaceholder')" required/>
                <input v-model="form.password" type="password" :placeholder="t('register.passwordPlaceholder')"
                       required/>
                <input v-model="form.password_confirmation" type="password"
                       :placeholder="t('register.passwordConfirmationPlaceholder')" required/>
                <button type="submit">{{ t('register.submitButton') }}</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import {useForm} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {useI18n} from '@/Lang/useI18n'

const {t} = useI18n()

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const register = () => {
    form.post('/register')
}
</script>

<style scoped>
/* Фон страницы как в Scanner */
:deep(body) {
    background: #f8fafc;
    min-height: 100vh;
}

/* Карточка */
.auth-form {
    max-width: 420px;
    margin: 5rem auto;
    padding: 2.5rem 2.5rem;
    border-radius: 24px;

    background: #ffffff;
    color: #0f172a;

    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;

    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
}

/* Заголовок — ТОТ ЖЕ градиент */
h2 {
    font-weight: 800;
    font-size: 2.3rem;
    margin-bottom: 2rem;
    text-align: center;

    background: linear-gradient(135deg, #e095bc, #bd6592);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Форма */
form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

/* Инпуты как в scanner (.device-select / .result-textarea) */
input {
    padding: 12px 18px;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;

    background-color: #f8fafc;
    color: #475569;

    font-size: 1rem;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

/* Фокус — ТОТ ЖЕ */
input:focus {
    outline: none;
    border-color: #e095bc;
    box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.15);
}

/* Кнопка — ТОТ ЖЕ градиент */
button {
    margin-top: 0.5rem;
    padding: 14px 28px;

    border: none;
    border-radius: 999px;

    font-size: 1.05rem;
    font-weight: 700;

    color: #ffffff;
    cursor: pointer;

    background: linear-gradient(135deg, #e095bc, #bd6592);
    box-shadow: 0 14px 32px rgba(236, 72, 153, 0.35);

    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Hover — как .scan-btn */
button:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 20px 45px rgba(236, 72, 153, 0.55);
}

/* Active */
button:active {
    transform: translateY(0);
}

/* Адаптив */
@media (max-width: 500px) {
    .auth-form {
        margin: 2rem 1rem;
        padding: 2rem 1.5rem;
    }
}
</style>
