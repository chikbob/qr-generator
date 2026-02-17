<template>
    <header class="app-header" @click.outside="closeDropdown">
        <nav>
            <Link href="/" class="nav-link">{{ t('header.home') }}</Link>
            <Link href="/generate" class="nav-link">{{ t('header.generate') }}</Link>
            <Link href="/scan" class="nav-link">{{ t('header.scan') }}</Link>

            <template v-if="user">
                <Link href="/history" class="nav-link">{{ t('header.history') }}</Link>
                <Link href="/profile" class="nav-link">{{ t('header.profile') }}</Link>
                <Link href="/plans" class="nav-link">{{ t('header.plans') }}</Link>
                <Link href="/contacts" class="nav-link">{{ t('header.contacts') }}</Link>

                <form @submit.prevent="logout">
                    <button type="submit" class="logout-btn">{{ t('header.logout') }}</button>
                </form>
            </template>

            <template v-else>
                <Link href="/login" class="nav-link">{{ t('header.login') }}</Link>
                <Link href="/register" class="nav-link">{{ t('header.register') }}</Link>
            </template>

            <!-- Кастомный селект языка -->
            <div
                class="lang-select-wrapper"
                ref="dropdownRef"
                @click.stop="toggleDropdown"
                :aria-expanded="isOpen.toString()"
                role="button"
                tabindex="0"
                @keydown.enter.prevent="toggleDropdown"
                @keydown.space.prevent="toggleDropdown"
                @keydown.arrow-down.prevent="focusNextOption"
                @keydown.arrow-up.prevent="focusPrevOption"
            >
        <span class="lang-selected">
          <span class="lang-code">{{ selectedLang.toUpperCase() }}</span>
          <svg class="arrow" width="12" height="8" viewBox="0 0 12 8" aria-hidden="true">
            <path d="M1 1l5 5 5-5" stroke="#bd6592" stroke-width="2" fill="none" fill-rule="evenodd"/>
          </svg>
        </span>

                <ul
                    v-if="isOpen"
                    class="lang-options"
                    role="listbox"
                    tabindex="-1"
                    ref="optionsListRef"
                >
                    <li
                        v-for="(lang, code, index) in flags"
                        :key="code"
                        class="lang-option"
                        :class="{ 'is-selected': code === selectedLang }"
                        role="option"
                        :aria-selected="code === selectedLang"
                        tabindex="0"
                        @click.stop="selectLang(code)"
                        @keydown.enter.prevent="selectLang(code)"
                        @keydown.space.prevent="selectLang(code)"
                        @keydown.arrow-down.prevent="focusNextOption(index)"
                        @keydown.arrow-up.prevent="focusPrevOption(index)"
                    >
                        {{ code.toUpperCase() }}
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script setup>
import {useForm, usePage, Link} from '@inertiajs/vue3'
import {computed, inject, ref, watch, nextTick} from 'vue'
import {onClickOutside} from '@vueuse/core'

const {t, setLang, currentLang} = inject('i18n')

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

// Языки с флагами
const flags = {
    ru: {flag: '🇷🇺'},
    ua: {flag: '🇺🇦'},
    en: {flag: '🇺🇸'},
}

const selectedLang = ref(currentLang.value)
const isOpen = ref(false)

const dropdownRef = ref(null)
const optionsListRef = ref(null)

// Закрыть селект при клике вне
onClickOutside(dropdownRef, () => {
    isOpen.value = false
})

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        nextTick(() => {
            // Фокус на первый элемент списка
            const firstOption = optionsListRef.value?.querySelector('.lang-option')
            firstOption?.focus()
        })
    }
}

const selectLang = (code) => {
    selectedLang.value = code
    setLang(code)
    isOpen.value = false
}

watch(currentLang, (newLang) => {
    selectedLang.value = newLang
})

// Управление клавиатурой для фокуса по стрелкам
function focusNextOption(currentIndex = -1) {
    nextTick(() => {
        const options = optionsListRef.value?.querySelectorAll('.lang-option')
        if (!options || options.length === 0) return
        const nextIndex = (currentIndex + 1) % options.length
        options[nextIndex].focus()
    })
}

function focusPrevOption(currentIndex = -1) {
    nextTick(() => {
        const options = optionsListRef.value?.querySelectorAll('.lang-option')
        if (!options || options.length === 0) return
        let prevIndex = currentIndex - 1
        if (prevIndex < 0) prevIndex = options.length - 1
        options[prevIndex].focus()
    })
}
</script>

<style scoped lang="scss">
.app-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(248, 250, 252, 0.95);
    backdrop-filter: saturate(180%) blur(18px);
    border-bottom: 1px solid #fce7f3;
    box-shadow: 0 4px 16px rgba(225, 108, 167, 0.18);
}

nav {
    max-width: 1200px;
    margin: 0 auto;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.nav-link {
    color: #0f172a;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 24px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link:hover {
    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
    color: #bd6592;
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(225, 108, 167, 0.2);
}

.router-link-exact-active {
    background: linear-gradient(135deg, #e095bc, #bd6592) !important;
    color: #fff !important;
    box-shadow: 0 6px 22px rgba(189, 101, 146, 0.45) !important;
}

.logout-btn {
    background: #fff;
    color: #bd6592;
    border: 1.5px solid #fbcfe8;
    padding: 10px 24px;
    border-radius: 24px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background: #fce7f3;
    box-shadow: 0 8px 24px rgba(189, 101, 146, 0.3);
    transform: scale(1.05);
}

/* === Селект языка === */
.lang-select-wrapper {
    position: relative;
    margin-left: auto;
    user-select: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    color: #0f172a;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    background: #fff;
    padding: 8px 18px;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: space-between;
    transition: all 0.25s ease;
}

.lang-select-wrapper:hover {
    background: #fce7f3;
    border-color: #fbcfe8;
}

.lang-selected {
    display: flex;
    align-items: center;
    gap: 6px;
}

.arrow {
    transition: transform 0.3s ease;
}

.lang-select-wrapper[aria-expanded='true'] .arrow {
    transform: rotate(180deg);
}

.lang-options {
    position: absolute;
    top: 110%;
    right: 0;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(189, 101, 146, 0.25);
    list-style: none;
    margin: 0;
    padding: 0.5rem 0;
    width: 90px;
    z-index: 100;
}

.lang-option {
    padding: 8px 16px;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease;
    outline: none;
}

.lang-option.is-selected {
    font-weight: 700;
    color: #bd6592;
    background-color: #fce7f3;
}

.lang-option:hover {
    background-color: #fce7f3;
}
</style>
