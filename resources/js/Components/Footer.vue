<template>
    <footer class="app-footer">
        <p>QR Code Generator © {{ currentYear }}</p>
    </footer>
</template>

<script setup>
import { computed } from 'vue'

const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
.app-footer {
    background: #2c3e50;
    color: white;
    text-align: center;
    padding: 15pxА
}
</style>
