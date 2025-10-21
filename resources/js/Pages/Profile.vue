<template>
    <AppLayout>
        <div class="profile-page">
            <h2>Профіль користувача</h2>
            <p><strong>Ім'я:</strong> {{ user?.name ?? 'Невідомо' }}</p>
            <p><strong>Email:</strong> {{ user?.email ?? 'Невідомо' }}</p>

            <form @submit.prevent="logout">
                <button type="submit">Вийти</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import {usePage, useForm} from '@inertiajs/vue3'
import {computed} from 'vue'
import AppLayout from "@/Layouts/AppLayout.vue";

const page = usePage()
const user = computed(() => page.props.auth.user)

const form = useForm({})
const logout = () => form.post('/logout')
</script>

<style scoped>
.profile-page {
    max-width: 600px;
    margin: 80px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

button {
    margin-top: 20px;
    background: #e74c3c;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 4px;
}
</style>
