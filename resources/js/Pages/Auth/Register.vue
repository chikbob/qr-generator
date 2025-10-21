<template>
    <AppLayout>
        <div class="auth-form">
            <h2>Реєстрація</h2>
            <form @submit.prevent="register">
                <input v-model="form.name" type="text" placeholder="Ім'я" required />
                <input v-model="form.email" type="email" placeholder="Email" required />
                <input v-model="form.password" type="password" placeholder="Пароль" required />
                <input v-model="form.password_confirmation" type="password" placeholder="Підтвердити пароль" required />
                <button type="submit">Зареєструватися</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

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
.auth-form {
    max-width: 400px;
    margin: 80px auto;
    padding: 30px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
input {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
}
button {
    background: #42b983;
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    width: 100%;
}
</style>
