<template>
    <header class="app-header">
        <nav>
            <Link href="/">Головна</Link>
            <Link href="/generate">Генератор</Link>
            <Link href="/scan">Сканер</Link>

            <template v-if="user">
                <Link href="/history">Історія</Link>
                <Link href="/profile">Профіль</Link>
                <Link href="/plans">Тарифи</Link>
                <Link href="/contacts">Контакти</Link>
                <form @submit.prevent="logout">
                    <button type="submit" class="logout-btn">Вийти</button>
                </form>
            </template>


            <template v-else>
                <Link href="/login">Увійти</Link>
                <Link href="/register">Реєстрація</Link>
            </template>
        </nav>
    </header>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const form = useForm({})

const logout = () => {
    if (typeof route === 'function') {
        form.post(route('logout'))
    } else {
        form.post('/logout')
    }
}
</script>

<style lang="scss" scoped>
.app-header {
    background: #2c3e50;
    padding: 15px;
}

nav {
    display: flex;
    gap: 15px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

nav a {
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background 0.2s;
}

nav a:hover {
    background: #34495e;
}

nav a.router-link-exact-active {
    background: #42b983;
}

/* Кнопка выхода */
.logout-btn {
    background: #e74c3c;
    font-weight: 600;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 7px 10px;
    cursor: pointer;

    &:hover {
        background: #c54033;
    }
}

/* Новая ссылка обратной связи */
.feedback-link {
    background: #3498db;
}
.feedback-link:hover {
    background: #2980b9;
}
</style>
