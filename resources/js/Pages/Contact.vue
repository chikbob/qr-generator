<template>
    <AppLayout>
        <div class="contact-container">
            <h2>
                {{ $t('contacts.title') }}
            </h2>

            <div class="contact-card">
                <div class="contact-info">
                    <p>
                        <strong>{{ $t('contacts.email') }}:</strong>
                        support@qrapp.com
                    </p>
                    <p>
                        <strong>{{ $t('contacts.phone') }}:</strong>
                        {{ $t('contacts.number') }}
                    </p>
                    <p>
                        <strong>{{ $t('contacts.address') }}:</strong>
                        {{ $t('contacts.addressValue') }}
                    </p>
                </div>

                <div class="feedback-header">
                    <h2>{{ $t('contacts.feedback.title') }}</h2>
                    <Link href="/feedback" class="feedback-link">
                        {{ $t('contacts.feedback.viewAll') }} →
                    </Link>
                </div>

                <form @submit.prevent="submit" class="feedback-form">
                    <div class="form-group">
                        <label for="subject">
                            {{ $t('contacts.form.subject') }}
                        </label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            :placeholder="$t('contacts.form.subjectPlaceholder')"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="category">
                            {{ $t('contacts.form.category') }}
                        </label>
                        <select id="category" v-model="form.category" required>
                            <option value="general">
                                {{ $t('categories.general') }}
                            </option>
                            <option value="bug">
                                {{ $t('categories.bug') }}
                            </option>
                            <option value="idea">
                                {{ $t('categories.idea') }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">
                            {{ $t('contacts.form.message') }}
                        </label>
                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="5"
                            :placeholder="$t('contacts.form.messagePlaceholder')"
                            required
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="submit-btn"
                        :disabled="form.processing">
                        {{ $t('contacts.form.submit') }}
                    </button>
                </form>

                <transition name="fade">
                    <div
                        v-if="form.recentlySuccessful"
                        class="success-message">
                        {{ $t('contacts.form.success') }}
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
$accent: #e095bc;
$accent-dark: #bd6592;
$accent-soft: #fce7f3;

$text-main: #0f172a;
$text-secondary: #475569;
$border: #e2e8f0;
$bg-soft: #f8fafc;

.contact-container {
    max-width: 900px;
    margin: 0 auto 4rem;
    padding: 0 20px;
    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;

    h2 {
        font-weight: 800;
        font-size: 2.4rem;
        margin-bottom: 2rem;
        text-align: center;
        background: linear-gradient(135deg, $accent, $accent-dark);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        user-select: none;
    }
}

.contact-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 3rem;
    border: 1.5px solid $border;
    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
    transition: all 0.3s ease;

    &:hover {
        transform: translateY(-6px);
        box-shadow: 0 35px 70px rgba(236, 72, 153, 0.15);
    }
}

.contact-info {
    background: $bg-soft;
    border: 1.5px solid $border;
    border-radius: 18px;
    padding: 2rem;
    margin-bottom: 2.5rem;
    color: $text-main;
    font-size: 1.05rem;
    font-weight: 500;

    p {
        margin: 12px 0;
        display: flex;
        align-items: center;
        gap: 12px;

        strong {
            color: $accent-dark;
            min-width: 100px;
        }
    }

    p::before {
        content: '';
        width: 20px;
        height: 20px;
        background-size: contain;
        background-repeat: no-repeat;
        opacity: 0.8;
    }

    p:nth-child(1)::before {
        background-image: url("data:image/svg+xml,%3Csvg fill='%23bd6592' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5Z'/%3E%3C/svg%3E");
    }

    p:nth-child(2)::before {
        background-image: url("data:image/svg+xml,%3Csvg fill='%23bd6592' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.11-.21c1.2.48 2.5.74 3.85.74a1 1 0 0 1 1 1v3.5a1 1 0 0 1-1 1A17 17 0 0 1 3 6a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.35.26 2.65.74 3.85a1 1 0 0 1-.21 1.11Z'/%3E%3C/svg%3E");
    }

    p:nth-child(3)::before {
        background-image: url("data:image/svg+xml,%3Csvg fill='%23bd6592' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7Zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z'/%3E%3C/svg%3E");
    }
}

.feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;

    h2 {
        margin: 0;
        font-size: 1.5rem;
        color: $text-main;
        font-weight: 700;
    }

    .feedback-link {
        color: $accent-dark;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s ease;

        &:hover {
            color: $accent;
        }
    }
}

.feedback-form {
    display: flex;
    flex-direction: column;
    gap: 1.8rem;

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;

        label {
            font-weight: 600;
            color: $text-secondary;
        }

        input,
        select,
        textarea {
            border: 1.5px solid $border;
            border-radius: 16px;
            font-size: 1rem;
            padding: 14px 16px;
            transition: all 0.3s ease;
            color: $text-main;
            background: $bg-soft;

            &:focus {
                border-color: $accent;
                box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.15);
                background: #ffffff;
                outline: none;
            }
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }
    }

    .submit-btn {
        background: linear-gradient(135deg, $accent, $accent-dark);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 14px 32px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        align-self: flex-start;
        box-shadow: 0 14px 32px rgba(236, 72, 153, 0.4);
        transition: all 0.3s ease;

        &:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(236, 72, 153, 0.5);
        }

        &:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            box-shadow: none;
        }
    }

    .success-message {
        margin-top: 1.5rem;
        background: $accent-soft;
        border-left: 4px solid $accent-dark;
        color: $accent-dark;
        padding: 14px 20px;
        border-radius: 14px;
        text-align: center;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(236, 72, 153, 0.12);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@media (max-width: 768px) {
    .contact-card {
        padding: 2rem;
    }

    .feedback-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

