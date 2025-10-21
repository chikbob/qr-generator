<template>
    <AppLayout>
        <div class="home-view">
            <h1>Ласкаво просимо до Генератора QR-кодів</h1>

            <div class="features">
                <div class="feature-card">
                    <h3>Генератор QR-кодів</h3>
                    <p>Створюйте QR-коди для тексту, URL, Wi-Fi та контактів</p>
                    <a href="/generate" class="feature-link">Перейти</a>
                </div>

                <div class="feature-card">
                    <h3>Сканер QR-кодів</h3>
                    <p>Скануйте QR-коди за допомогою камери пристрою</p>
                    <a href="/scan" class="feature-link">Перейти</a>
                </div>

                <div class="feature-card">
                    <h3>Історія</h3>
                    <p>Переглядайте історію створених QR-кодів</p>
                    <a href="/history" class="feature-link">Перейти</a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
</script>

<style scoped>
.home-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 40px;
}

.feature-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.feature-link {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 16px;
    background: #42b983;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}
</style>
