<template>
    <AuthenticatedLayout>
        <Head title="Verify Email" />

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto">
                <div class="mb-4 text-sm text-gray-600">
                    Спасибо за регистрацию! Перед началом работы, пожалуйста, проверьте вашу электронную почту на предмет ссылки подтверждения. Если вы не получили письмо, мы с удовольствием отправим вам другое.
                </div>

                <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
                    На ваш адрес электронной почты была отправлена новая ссылка подтверждения.
                </div>

                <form @submit.prevent="submit">
                    <div class="flex items-center justify-between mt-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Resend Verification Email
                        </PrimaryButton>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Log Out
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: String,
});

const verificationLinkSent = ref(false);

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => {
            verificationLinkSent.value = true;
        },
    });
};
</script>
