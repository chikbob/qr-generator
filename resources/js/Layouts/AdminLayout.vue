<template>
    <div class="admin-layout">
        <AdminHeader :tables="tables"/>
        <main class="admin-main">
            <slot/>
        </main>
    </div>
</template>

<script setup>
import {computed} from 'vue'
import {usePage} from '@inertiajs/vue3'
import AdminHeader from '@/Components/AdminHeader.vue'

const page = usePage()
const tables = computed(() => page.props.tables || [])
</script>

<style scoped>
.admin-layout {
    min-height: 100dvh;
    background:
        radial-gradient(circle at top left, rgba(251, 207, 232, 0.42), transparent 34%),
        radial-gradient(circle at bottom right, rgba(254, 205, 211, 0.35), transparent 28%),
        linear-gradient(180deg, #fff8fb 0%, #fff5f8 38%, #fffdfd 100%);
}

.admin-main {
    padding: clamp(14px, 2vw, 22px) clamp(12px, 2vw, 18px) clamp(24px, 4vw, 32px);
}
</style>
