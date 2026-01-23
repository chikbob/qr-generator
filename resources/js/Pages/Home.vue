<template>
    <AppLayout>
        <div class="home-view">
            <h1>{{ t('home.title') }}</h1>

            <div class="features">
                <div class="feature-card">
                    <h3>{{ t('home.generator.title') }}</h3>
                    <p>{{ t('home.generator.text') }}</p>
                    <a href="/generate" class="feature-link">
                        {{ t('common.go') }}
                    </a>
                </div>

                <div class="feature-card">
                    <h3>{{ t('home.scanner.title') }}</h3>
                    <p>{{ t('home.scanner.text') }}</p>
                    <a href="/scan" class="feature-link">
                        {{ t('common.go') }}
                    </a>
                </div>

                <div class="feature-card">
                    <h3>{{ t('home.history.title') }}</h3>
                    <p>{{ t('home.history.text') }}</p>
                    <a href="/history" class="feature-link">
                        {{ t('common.go') }}
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {useI18n} from '@/lang/useI18n'

const {t} = useI18n()
</script>

<style scoped>
.home-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
    color: #2c3e50;
}

h1 {
    font-size: 2.4rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: #34495e;
    text-align: center;
}

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.feature-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    background: #fff;
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    text-align: center;
    cursor: pointer;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgb(0 0 0 / 0.1);
}

.feature-card h3 {
    margin-bottom: 0.75rem;
    color: #42b983;
}

.feature-card p {
    color: #555;
    font-size: 1rem;
}

.feature-link {
    margin-top: 1.2rem;
    display: inline-block;
    background-color: #42b983;
    color: white;
    padding: 10px 22px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.feature-link:hover {
    background-color: #369d6f;
}
</style>
