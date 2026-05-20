<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Подтверждение email" />

        <div
            class="w-full max-w-[420px] rounded-[20px] border p-8 md:p-10"
            style="background-color: #1A1F2B; border-color: #2D3748; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);"
        >
            <div class="mb-4 text-sm" style="color: #94A3B8;">
                Спасибо за регистрацию! Прежде чем начать, подтвердите email, перейдя по ссылке из письма.
                Если письмо не пришло, мы отправим ещё одно.
            </div>

            <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-emerald-400">
                Новая ссылка отправлена на ваш email.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl py-3 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);"
                    :style="form.processing ? { opacity: 0.7, transform: 'none' } : {}"
                >
                    Отправить повторно
                </button>

                <div class="text-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm transition-colors hover:text-violet-300"
                        style="color: #7C3AED;"
                    >
                        Выйти
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
