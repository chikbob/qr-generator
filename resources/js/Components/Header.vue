<template>
    <header class="app-header">
        <nav>
            <Link href="/">{{ t('header.home') }}</Link>
            <Link href="/generate">{{ t('header.generate') }}</Link>
            <Link href="/scan">{{ t('header.scan') }}</Link>

            <template v-if="user">
                <Link href="/history">{{ t('header.history') }}</Link>
                <Link href="/profile">{{ t('header.profile') }}</Link>
                <Link href="/plans">{{ t('header.plans') }}</Link>
                <Link href="/contacts">{{ t('header.contacts') }}</Link>

                <form @submit.prevent="logout">
                    <button type="submit" class="logout-btn">
                        {{ t('header.logout') }}
                    </button>
                </form>
            </template>

            <template v-else>
                <Link href="/login">{{ t('header.login') }}</Link>
                <Link href="/register">{{ t('header.register') }}</Link>
            </template>

            <!-- 🌍 Селектор языка -->
            <select
                class="lang-select"
                v-model="currentLang"
                @change="setLang(currentLang)"
            >
                <option value="ru">RU</option>
                <option value="ua">UA</option>
                <option value="en">EN</option>
            </select>
        </nav>
    </header>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3'
import { computed, inject } from 'vue'

const { t, setLang, currentLang } = inject('i18n')

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

.lang-select {
    background: #34495e;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 6px 8px;
    font-weight: 600;
    cursor: pointer;

    option {
        color: #000;
    }
}
</style>
