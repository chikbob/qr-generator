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
import {useI18n} from '@/Lang/useI18n'

const {t} = useI18n()
</script>

<style scoped lang="scss">
$accent: #e095bc;
$accent-dark: #bd6592;
$accent-soft: #fce7f3;

$text-main: #0f172a;
$text-secondary: #475569;
$border: #e2e8f0;
$bg-soft: #f8fafc;

.home-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 1.5rem;
    border-radius: 24px;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

h1 {
    font-size: 2.6rem;
    font-weight: 800;
    text-align: center;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, $accent, $accent-dark);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.features {
    margin-top: 3rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: #ffffff;
    border: 1.5px solid $border;
    border-radius: 24px;
    padding: 2.5rem 2rem;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);

    &:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px rgba(236, 72, 153, 0.15);
    }

    h3 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: $accent-dark;
    }

    p {
        color: $text-secondary;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
}

.feature-link {
    display: inline-block;
    background: linear-gradient(135deg, $accent, $accent-dark);
    color: #ffffff;
    padding: 14px 32px;
    border-radius: 999px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 14px 32px rgba(236, 72, 153, 0.35);

    &:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 20px 45px rgba(236, 72, 153, 0.55);
    }
}

@media (max-width: 768px) {
    .home-view {
        padding: 2rem 1rem;
    }

    h1 {
        font-size: 2rem;
    }

    .feature-card {
        padding: 2rem 1.5rem;
    }

    .feature-link {
        width: 100%;
    }
}
</style>
