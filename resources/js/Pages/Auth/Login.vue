<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const mode = ref('login')   // 'login' | 'forgot'

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
})

const forgotForm = useForm({
    email: '',
})

function submitLogin() {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    })
}

function submitForgot() {
    forgotForm.post(route('password.email'))
}
</script>

<template>
    <GuestLayout>
        <Head title="Вход" />

        <div
            class="w-full max-w-[420px] rounded-[20px] border p-8 md:p-10"
            style="background-color: #1A1F2B; border-color: #2D3748; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);"
        >
            <!-- Логотип -->
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl" style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>

            <h1 class="mt-6 text-center text-[26px] font-bold leading-tight text-white">
                Добро пожаловать в<br>SoundERP
            </h1>
            <p class="mt-2 text-center text-sm" style="color: #94A3B8;">
                Управляйте своим лейблом эффективно
            </p>

            <div class="relative mt-8 min-h-[260px]">
                <Transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform translate-y-3 opacity-0"
                    enter-to-class="transform translate-y-0 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform translate-y-0 opacity-100"
                    leave-to-class="transform -translate-y-3 opacity-0"
                >

                    <!-- LOGIN -->
                    <form v-if="mode === 'login'" key="login" @submit.prevent="submitLogin" class="space-y-4">
                        <div>
                            <input
                                v-model="loginForm.email"
                                type="email"
                                required
                                placeholder="Email"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                                style="background-color: #0B0E14; border-color: #2D3748;"
                            />
                            <p v-if="loginForm.errors.email" class="mt-1 text-xs text-red-400">{{ loginForm.errors.email }}</p>
                        </div>

                        <div>
                            <input
                                v-model="loginForm.password"
                                type="password"
                                required
                                placeholder="Пароль"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                                style="background-color: #0B0E14; border-color: #2D3748;"
                            />
                            <p v-if="loginForm.errors.password" class="mt-1 text-xs text-red-400">{{ loginForm.errors.password }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="loginForm.processing"
                            class="relative mt-2 w-full rounded-xl py-3 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-0.5"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);"
                            :style="loginForm.processing ? { opacity: 0.7, transform: 'none' } : {}"
                        >
                            Войти
                        </button>

                        <div class="pt-2 text-center">
                            <button
                                type="button"
                                @click="mode = 'forgot'"
                                class="text-sm transition-colors hover:text-violet-300"
                                style="color: #7C3AED;"
                            >
                                Забыли пароль?
                            </button>
                        </div>

                        <div class="pt-2 text-center"> 
                            <Link :href="route('register')" 
                            class="text-sm transition-colors 
                            hover:text-violet-300" 
                            style="color: #7C3AED;"
                            > 
                                Нет аккаунта? Зарегистрируйтесь!
                            </Link> 
                        </div>
                    </form>

                    <!-- FORGOT PASSWORD -->
                    <form v-else key="forgot" @submit.prevent="submitForgot" class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Восстановление пароля</h2>
                        <p class="text-sm" style="color: #94A3B8;">
                            Введите email, мы отправим ссылку для сброса
                        </p>

                        <div>
                            <input
                                v-model="forgotForm.email"
                                type="email"
                                required
                                placeholder="Email"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-white placeholder-gray-500 outline-none transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30"
                                style="background-color: #0B0E14; border-color: #2D3748;"
                            />
                            <p v-if="forgotForm.errors.email" class="mt-1 text-xs text-red-400">{{ forgotForm.errors.email }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="forgotForm.processing"
                            class="relative mt-2 w-full rounded-xl py-3 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-0.5"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.4);"
                            :style="forgotForm.processing ? { opacity: 0.7, transform: 'none' } : {}"
                        >
                            Отправить ссылку
                        </button>

                        <div class="pt-2 text-center">
                            <button
                                type="button"
                                @click="mode = 'login'"
                                class="text-sm transition-colors hover:text-violet-300"
                                style="color: #7C3AED;"
                            >
                                ← Назад ко входу
                            </button>
                        </div>

                        <div v-if="forgotForm.recentlySuccessful" class="rounded-lg border p-3 text-center text-sm text-emerald-400" style="background-color: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.2);">
                            Ссылка отправлена!
                        </div>
                    </form>

                </Transition>
            </div>
        </div>
    </GuestLayout>
</template>
