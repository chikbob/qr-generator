<template>
    <AppLayout>
        <div class="profile-page">
            <h2>Профіль користувача</h2>

            <p><strong>Ім'я:</strong> {{ user?.name ?? 'Невідомо' }}</p>
            <p><strong>Email:</strong> {{ user?.email ?? 'Невідомо' }}</p>
            <p>
                <strong>Тариф: </strong>
                <span v-if="user?.plan">
                    {{ user.plan.name }} ({{ user.plan.price }} USD)
                </span>
                <span v-else>Free</span>
            </p>

            <form @submit.prevent="logout">
                <button type="submit" class="btn-logout">Вийти</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { usePage, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from "@/Layouts/AppLayout.vue"

const page = usePage()
const user = computed(() => page.props.auth.user)

const form = useForm({})
const logout = () => form.post('/logout')
</script>

<style scoped>
.profile-page {
    max-width: 600px;
    margin: 3rem auto 5rem;
    padding: 0.5rem 2rem 2rem;
    background: #fff;
    border-radius: 8px;
    color: #2c3e50;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

h2 {
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #34495e;
    text-align: center;
}

p {
    font-size: 1rem;
    margin-bottom: 1.2rem;
    color: #34495e;
}

strong {
    font-weight: 600;
}

.btn-logout {
    display: block;
    width: 100%;
    padding: 12px 0;
    margin-top: 2rem;
    background-color: #f44336;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-logout:hover {
    background-color: #d32f2f;
}
</style>
