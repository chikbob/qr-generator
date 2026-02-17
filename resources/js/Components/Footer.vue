<template>
    <footer class="app-footer">
        <div class="footer-inner">
            <div class="footer-left">
                <strong>QR Code Generator</strong>
                <span>© {{ currentYear }}</span>
            </div>

            <div class="footer-right">
                <a href="/contacts">Contacts</a>
                <a href="/plans">Plans</a>
                <a href="/privacy">Privacy</a>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue'
const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped lang="scss">
.app-footer {
    margin-top: 5rem;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border-top: 1px solid #e2e8f0;
}

.footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.footer-left {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #475569;
    font-size: 0.95rem;
}

.footer-left strong {
    color: #0f172a;
}

.footer-right {
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
}

.footer-right a {
    color: #475569;
    font-size: 0.9rem;
    text-decoration: none;
    position: relative;
    transition: color 0.25s ease;
}

.footer-right a::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -3px;
    width: 0;
    height: 2px;
    background: #bd6592;
    transition: width 0.25s ease;
}

.footer-right a:hover {
    color: #bd6592;
}

.footer-right a:hover::after {
    width: 100%;
}
</style>
