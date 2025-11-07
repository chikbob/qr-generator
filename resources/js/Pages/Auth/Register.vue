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
    margin: 5rem auto;
    padding: 2rem 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 14px rgb(0 0 0 / 0.1);
    color: #2c3e50;
    text-align: center;
}

h2 {
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #34495e;
}

input {
    display: block;
    width: 100%;
    margin-bottom: 1rem;
    padding: 12px 14px;
    border-radius: 6px;
    border: 1.5px solid #ccc;
    font-size: 1rem;
    font-family: inherit;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: #42b983;
    box-shadow: 0 0 8px rgba(66, 185, 131, 0.5);
}

button {
    background: #42b983;
    color: white;
    border: none;
    padding: 12px;
    cursor: pointer;
    width: 100%;
    border-radius: 6px;
    font-weight: 700;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #369d6f;
}
</style>
