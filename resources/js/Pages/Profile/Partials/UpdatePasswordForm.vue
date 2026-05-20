<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-white">Обновить пароль</h2>
            <p class="mt-1 text-sm text-slate-400">Используйте длинный надёжный пароль для безопасности.</p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-5">
            <div>
                <label for="current_password" class="block text-sm text-slate-400 mb-2">Текущий пароль</label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                    style="background-color: #0B0E14; border-color: #2D3748;"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div>
                <label for="password" class="block text-sm text-slate-400 mb-2">Новый пароль</label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                    style="background-color: #0B0E14; border-color: #2D3748;"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm text-slate-400 mb-2">Подтверждение пароля</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                    style="background-color: #0B0E14; border-color: #2D3748;"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl px-6 py-2.5 text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                    :style="form.processing ? { opacity: 0.7, transform: 'none' } : {}"
                >
                    Сохранить
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-emerald-400">Сохранено.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
