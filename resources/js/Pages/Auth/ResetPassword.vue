<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Сброс пароля" />

        <div
            class="w-full max-w-[420px] rounded-[20px] border p-8 md:p-10"
            style="background-color: #1A1F2B; border-color: #2D3748; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);"
        >
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm text-slate-400 mb-2">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <label for="password" class="block text-sm text-slate-400 mb-2">Новый пароль</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm text-slate-400 mb-2">Подтверждение пароля</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl py-3 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);"
                    :style="form.processing ? { opacity: 0.7, transform: 'none' } : {}"
                >
                    Сбросить пароль
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
