<template>
    <header class="app-header">
        <nav>
            <a href="/">Головна</a>
            <a href="/generate">Генератор</a>
            <a href="/scan">Сканер</a>

            <template v-if="user">
                <a href="/history">Історія</a>
                <a href="/profile">Профіль</a>
                <button @click="logout" class="logout-btn">Вийти</button>
            </template>

            <template v-else>
                <a href="/login">Увійти</a>
                <a href="/register">Реєстрація</a>
                <button @click="logout" class="logout-btn">Вийти</button>
            </template>
        </nav>
    </header>
</template>

<script setup>
import {router, usePage} from '@inertiajs/vue3'
import {computed} from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

console.log(page.props)

const logout = () => {
    router.post('/logout')
}
</script>

<style scoped>
.app-header {
    background: #2c3e50;
    padding: 15px;
}

nav {
    display: flex;
    gap: 15px;
    justify-content: center;
    align-items: center;
}

nav a {
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px;
}

nav a.router-link-exact-active {
    background: #42b983;
}

.logout-btn {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
}
</style>
