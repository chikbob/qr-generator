<template>
    <header class="admin-header">
        <div class="inner">
            <div class="top-row">
                <div class="brand">
                    <span class="badge">ADMIN</span>
                    <Link href="/admin" class="title">{{ t('admin.nav.panel') }}</Link>
                </div>

                <div class="actions">
                    <div class="lang-switch" role="group" aria-label="Language switcher">
                        <button
                            v-for="lang in langs"
                            :key="lang"
                            type="button"
                            class="lang-btn"
                            :class="{ active: currentLang === lang }"
                            @click="changeLang(lang)"
                        >
                            {{ lang.toUpperCase() }}
                        </button>
                    </div>
                    <Link href="/" class="to-site">{{ t('admin.nav.toSite') }}</Link>
                </div>
            </div>

            <nav class="links" :aria-label="t('admin.nav.panel')">
                <Link href="/admin" class="nav-link">{{ t('admin.nav.dashboard') }}</Link>
                <Link
                    v-for="table in tables"
                    :key="table"
                    :href="`/admin/${table}`"
                    class="nav-link"
                >
                    {{ tableLabel(table) }}
                </Link>
            </nav>
        </div>
    </header>
</template>

<script setup>
import {Link} from '@inertiajs/vue3'
import {useI18n} from '@/Lang/useI18n'

const {t, tMaybe, setLang, currentLang} = useI18n()

defineProps({
    tables: {
        type: Array,
        default: () => [],
    },
})

const langs = ['ru', 'ua', 'en']

const changeLang = (lang) => {
    setLang(lang)
}

const tableLabel = (table) => {
    const localized = tMaybe(`admin.tables.${table}`)
    if (localized === `admin.tables.${table}`) {
        return table
    }
    return `${localized} (${table})`
}
</script>

<style scoped lang="scss">
.admin-header {
    position: sticky;
    top: 0;
    z-index: 60;
    background: linear-gradient(135deg, #fff3f0, #ffe4eb);
    border-bottom: 2px solid #f4a9bf;
    box-shadow: 0 10px 24px rgba(189, 101, 146, 0.18);
}

.inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.top-row {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 8px;
}

.badge {
    background: #be123c;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 800;
    border-radius: 999px;
    padding: 4px 10px;
}

.title {
    color: #7a1f47;
    font-weight: 800;
    text-decoration: none;
}

.links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    width: 100%;
}

.nav-link {
    white-space: nowrap;
    text-decoration: none;
    color: #6b213f;
    font-weight: 700;
    border: 1px solid #f5bfd0;
    border-radius: 999px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.7);
}

.nav-link:hover {
    border-color: #e095bc;
    background: #fff;
}

.to-site {
    text-decoration: none;
    color: #7a1f47;
    font-weight: 700;
}

.actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.lang-switch {
    display: inline-flex;
    border: 1px solid #f5bfd0;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.7);
    padding: 2px;
}

.lang-btn {
    border: none;
    background: transparent;
    color: #6b213f;
    font-weight: 700;
    font-size: 0.76rem;
    border-radius: 999px;
    padding: 5px 9px;
    cursor: pointer;
}

.lang-btn:hover,
.lang-btn:focus-visible {
    background: #fce7f3;
    color: #7a1f47;
    outline: none;
}

.lang-btn.active {
    background: #e095bc;
    color: #fff;
}

@media (max-width: 900px) {
    .top-row {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
