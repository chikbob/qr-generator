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
        radial-gradient(circle at 1px 1px, rgba(189, 101, 146, 0.14) 1px, transparent 0);
    background-size: 16px 16px;
}

.admin-main {
    padding: 18px;
}
</style>

