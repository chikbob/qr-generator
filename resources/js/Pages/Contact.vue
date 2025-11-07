<template>
    <AppLayout>
        <div class="contact-container">
            <h2 style="margin: 2rem 0; display: flex; justify-content: center; font-size: 1.8rem">Контакти</h2>

            <div class="contact-card">
                <div class="contact-info">
                    <p><strong>Пошта:</strong> support@qrapp.com</p>
                    <p><strong>Телефон:</strong> +380 67 123 4567</p>
                    <p><strong>Адреса:</strong> м. Київ, вул. Прикладна, 1</p>
                </div>

                <div class="feedback-header">
                    <h2>Залишити звернення</h2>
                    <Link href="/feedback" class="feedback-link">
                        Переглянути всі звернення →
                    </Link>
                </div>

                <form @submit.prevent="submit" class="feedback-form">
                    <div class="form-group">
                        <label for="subject">Тема</label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            placeholder="Введіть тему звернення"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="category">Категорія</label>
                        <select id="category" v-model="form.category" required>
                            <option value="general">Загальне питання</option>
                            <option value="bug">Помилка</option>
                            <option value="idea">Ідея / Пропозиція</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Повідомлення</label>
                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="5"
                            placeholder="Опишіть своє питання або проблему"
                            required
                        ></textarea>
                    </div>

                    <button type="submit" class="submit-btn" :disabled="form.processing">
                        Надіслати звернення
                    </button>
                </form>

                <transition name="fade">
                    <div v-if="form.recentlySuccessful" class="success-message">
                        Ваше звернення успішно надіслано!
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {useForm, Link} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
    subject: '',
    category: 'general',
    message: '',
})

const submit = () => {
    form.post(route('feedback.store'))
}
</script>

<style scoped lang="scss">
.contact-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 20px 30px;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 35px;
        font-weight: 600;
    }
}

.contact-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    padding: 40px;
}

.contact-info {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;

    p {
        margin: 6px 0;
        font-size: 1rem;
        color: #333;

        strong {
            color: #2c3e50;
        }
    }
}

.feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;

    h2 {
        margin: 0;
        font-size: 1.4rem;
        color: #2c3e50;
        font-weight: 600;
    }

    .feedback-link {
        color: #42b983;
        font-size: 0.95rem;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;

        &:hover {
            color: #2e8b63;
            text-decoration: underline;
        }
    }
}

/* === ЕДИНЫЙ СТИЛЬ ДЛЯ ВСЕХ ПОЛЕЙ === */
.feedback-form {
    display: flex;
    flex-direction: column;
    gap: 20px;

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;

        label {
            font-weight: 500;
            color: #444;
            font-size: 0.95rem;
        }

        input,
        select,
        textarea {
            max-width: 100%;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            font-size: 1rem;
            padding: 12px 14px;
            background: #fff;
            transition: all 0.2s ease;
            color: #2c3e50;

            &:focus {
                border-color: #42b983;
                box-shadow: 0 0 0 3px rgba(66, 185, 131, 0.2);
                outline: none;
            }
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }
    }

    .submit-btn {
        background-color: #42b983;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;

        &:hover:not(:disabled) {
            background-color: #3aa876;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        &:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    }

    .success-message {
        margin-top: 15px;
        background-color: #e8f5e9;
        border-left: 4px solid #42b983;
        color: #2e7d32;
        padding: 12px 16px;
        border-radius: 6px;
        text-align: center;
        font-size: 0.95rem;
    }
}

/* Анимация уведомления */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Адаптив */
@media (max-width: 768px) {
    .contact-card {
        padding: 25px;
    }

    .feedback-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>
