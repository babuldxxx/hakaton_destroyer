<script setup>
import { ref } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const role = ref('label') // 'label' | 'artist'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: role.value,
})

const submit = () => {
    form.role = role.value
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <GuestLayout>
        <Head title="Регистрация" />

        <div
            class="w-full max-w-[420px] rounded-[20px] border p-8 md:p-10"
            style="background-color: #1A1F2B; border-color: #2D3748; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);"
        >
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl" style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>

            <h1 class="mt-6 text-center text-[26px] font-bold leading-tight text-white">
                Создайте аккаунт
            </h1>
            <p class="mt-2 text-center text-sm" style="color: #94A3B8;">
                Выберите роль и заполните данные
            </p>

            <!-- Выбор роли -->
            <div class="mt-6 flex rounded-xl p-1" style="background-color: #13161f;">
                <button
                    type="button"
                    @click="role = 'label'"
                    :class="[
                        'flex-1 rounded-lg py-2.5 text-sm font-medium transition-all duration-200',
                        role === 'label'
                            ? 'text-white shadow-lg'
                            : 'text-[#64748B] hover:text-white hover:bg-white/5'
                    ]"
                    :style="role === 'label' ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
                >
                    Лейбл
                </button>
                <button
                    type="button"
                    @click="role = 'artist'"
                    :class="[
                        'flex-1 rounded-lg py-2.5 text-sm font-medium transition-all duration-200',
                        role === 'artist'
                            ? 'text-white shadow-lg'
                            : 'text-[#64748B] hover:text-white hover:bg-white/5'
                    ]"
                    :style="role === 'artist' ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
                >
                    Артист
                </button>
            </div>

            <form @submit.prevent="submit" class="mt-6 space-y-4">
                <div>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Имя"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">{{ form.errors.name }}</p>
                </div>

                <div>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="Email"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
                </div>

                <div>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="Пароль"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
                </div>

                <div>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        placeholder="Подтверждение пароля"
                        class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                        style="background-color: #0B0E14; border-color: #2D3748;"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-400">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="relative w-full rounded-xl py-3 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);"
                    :style="form.processing ? { opacity: 0.7, transform: 'none' } : {}"
                >
                    Зарегистрироваться
                </button>

                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="text-sm transition-colors hover:text-violet-300"
                        style="color: #7C3AED;"
                    >
                        Уже есть аккаунт?
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
