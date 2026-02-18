<template>
    <AppLayout>
        <div class="profile-page">
            <h2>{{ t('profile.title') }}</h2>

            <div class="profile-card">
                <div class="avatar">
                    <span v-if="user?.name">{{ userInitials }}</span>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="#db2777" viewBox="0 0 24 24" width="48" height="48">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div class="profile-info">
                    <div class="info-item">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="#db2777" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM12 14c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/>
                        </svg>
                        <div>
                            <div class="label">{{ t('profile.name') }}</div>
                            <div class="value">{{ user?.name ?? t('common.unknown') }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="#db2777" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <div>
                            <div class="label">{{ t('profile.email') }}</div>
                            <div class="value">{{ user?.email ?? t('common.unknown') }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="#db2777" viewBox="0 0 24 24">
                            <path d="M12 7a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm0-5c-4.97 0-9 4.03-9 9a9 9 0 1 0 18 0c0-4.97-4.03-9-9-9z"/>
                        </svg>
                        <div>
                            <div class="label">{{ t('profile.plan') }}</div>
                            <div class="value">
                <span v-if="user?.plan">
                  {{ user.plan.name }} ({{ user.plan.price }} USD)
                </span>
                                <span v-else>
                  {{ t('profile.free') }}
                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="logout" class="logout-form">
                <button type="submit" class="btn-logout">{{ t('common.logout') }}</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { usePage, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useI18n } from '@/Lang/useI18n'

const { t } = useI18n()
const page = usePage()
const user = computed(() => page.props.auth.user)

const form = useForm({})
const logout = () => form.post('/logout')

// Генерируем инициалы из имени пользователя
const userInitials = computed(() => {
    if (!user.value?.name) return ''
    return user.value.name
        .split(' ')
        .map(n => n[0].toUpperCase())
        .join('')
})
</script>

<style scoped lang="scss">
$accent: #e095bc;
$accent-dark: #bd6592;
$accent-soft: #fce7f3;

$text-main: #0f172a;
$text-secondary: #475569;
$border: #e2e8f0;
$bg-soft: #f8fafc;

.profile-page {
    max-width: 700px;
    margin: 3rem auto 5rem;
    padding: 2.5rem 3rem;
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    color: $text-main;
    display: flex;
    flex-direction: column;
    gap: 2.5rem;

    h2 {
        font-weight: 800;
        font-size: 2.4rem;
        text-align: center;
        background: linear-gradient(135deg, $accent, $accent-dark);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
    }
}

.profile-card {
    display: flex;
    gap: 2.5rem;
    align-items: center;
    padding: 2.5rem;
    border-radius: 24px;
    background: #ffffff;
    border: 1.5px solid $border;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;

    &:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 60px rgba(236, 72, 153, 0.15);
    }
}

.avatar {
    flex-shrink: 0;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, $accent, $accent-dark);
    color: #ffffff;
    font-weight: 800;
    font-size: 2.6rem;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 14px 32px rgba(236, 72, 153, 0.35);
    user-select: none;

    svg {
        width: 56px;
        height: 56px;
        fill: #ffffff;
    }
}

.profile-info {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 1.4rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: $bg-soft;
    padding: 1rem 1.5rem;
    border-radius: 16px;
    border: 1px solid $border;
    transition: all 0.25s ease;

    &:hover {
        border-color: $accent;
        box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.12);
    }
}

.icon {
    flex-shrink: 0;
    width: 26px;
    height: 26px;
    fill: $accent-dark;
}

.label {
    font-weight: 600;
    font-size: 0.95rem;
    color: $text-secondary;
}

.value {
    font-weight: 700;
    font-size: 1.2rem;
    color: $accent-dark;
    user-select: text;
}

.logout-form {
    margin-top: 1rem;
    width: 100%;
}

.btn-logout {
    width: 100%;
    padding: 16px 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, $accent, $accent-dark);
    border: none;
    border-radius: 999px;
    cursor: pointer;
    box-shadow: 0 14px 32px rgba(236, 72, 153, 0.45);
    transition: all 0.3s ease;

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 45px rgba(236, 72, 153, 0.55);
    }
}

@media (max-width: 600px) {
    .profile-card {
        flex-direction: column;
        padding: 2rem;
        gap: 2rem;
    }

    .avatar {
        width: 85px;
        height: 85px;
        font-size: 2rem;
    }

    .info-item {
        padding: 0.9rem 1rem;
    }

    .value {
        font-size: 1.05rem;
    }
}
</style>

