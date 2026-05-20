<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-white">Удалить аккаунт</h2>
            <p class="mt-1 text-sm text-slate-400">
                После удаления все данные будут безвозвратно потеряны. Сохраните нужную информацию перед удалением.
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="rounded-xl px-6 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition"
        >
            Удалить аккаунт
        </button>

        <!-- Модальное окно -->
        <div v-if="confirmingUserDeletion" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="background-color: rgba(0,0,0,0.7);">
            <div class="w-full max-w-md rounded-xl border p-6" style="background-color: #1A1F2B; border-color: #2D3748;">
                <h2 class="text-lg font-semibold text-white mb-2">Вы уверены?</h2>
                <p class="text-sm text-slate-400 mb-6">
                    Введите пароль для подтверждения удаления аккаунта.
                </p>

                <div class="mb-4">
                    <input
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Пароль"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">{{ form.errors.password }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="closeModal"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-400 bg-[#0B0E14] border border-[#2D3748] hover:text-white transition"
                    >
                        Отмена
                    </button>
                    <button
                        @click="deleteUser"
                        :disabled="form.processing"
                        class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition"
                        :style="form.processing ? { opacity: 0.7 } : {}"
                    >
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
