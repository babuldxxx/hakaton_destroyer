<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Добро пожаловать" />
    <div class="min-h-screen flex flex-col bg-[#0B0E14] text-white font-sans">
        <!-- Header -->
        <header class="flex items-center justify-between px-6 py-5 max-w-6xl mx-auto w-full">
            <Link href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20"
                     style="background: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3a1 1 0 0 1 1 1v16a1 1 0 1 1-2 0V4a1 1 0 0 1 1-1zM7 7a1 1 0 0 1 1 1v8a1 1 0 1 1-2 0V8a1 1 0 0 1 1-1zM17 6a1 1 0 0 1 1 1v10a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1zM2 11a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1zM22 10a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0v-4a1 1 0 0 1 1-1z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-violet-400 to-blue-400">
                    SoundERP
                </span>
            </Link>
            <nav v-if="canLogin" class="flex items-center gap-3">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="route('dashboard')"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                    >
                        Дашборд
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold border border-slate-500 text-slate-300 hover:text-white hover:border-slate-400 transition"
                    >
                        Вход
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                    >
                        Регистрация
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero -->
        <section class="flex-1 flex flex-col items-center justify-center text-center px-6 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
                Управляйте своим музыкальным лейблом эффективно
            </h1>
            <p class="text-lg text-slate-400 max-w-2xl mb-12">
                SoundERP автоматизирует учёт доходов от стриминговых площадок, распределяет роялти между артистами и помогает видеть полную картину бизнеса в реальном времени.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl mb-16">
                <!-- Card 1 -->
                <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-emerald-500/20 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Для лейбла</h3>
                    <p class="text-sm text-slate-400">Загружайте отчёты дистрибьюторов, отслеживайте доходы по трекам и площадкам, управляйте артистами.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-violet-500/20 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Для артиста</h3>
                    <p class="text-sm text-slate-400">Просматривайте свои треки, доли и начисленные роялти в реальном времени. Прозрачность выплат.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-blue-500/20 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Аналитика и выплаты</h3>
                    <p class="text-sm text-slate-400">Наглядные графики, автоматическое распределение авторских и смежных прав, управление выплатами.</p>
                </div>
            </div>

            <div v-if="canLogin && !$page.props.auth.user" class="flex gap-4">
                <Link
                    :href="route('login')"
                    class="px-8 py-3 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                >
                    Войти
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="px-8 py-3 rounded-xl text-sm font-semibold border border-slate-500 text-slate-300 hover:text-white hover:border-slate-400 transition"
                >
                    Зарегистрироваться
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 text-center text-sm text-slate-500">
            SoundERP &copy; {{ new Date().getFullYear() }}. Все права защищены.
        </footer>
    </div>
</template>
