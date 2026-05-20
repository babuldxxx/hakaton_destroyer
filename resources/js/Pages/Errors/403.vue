<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex items-center justify-center min-h-[80vh]">
            <div class="text-center max-w-md">
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 rounded-full bg-red-500/10 flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-4xl font-bold text-white mb-2">403</h1>
                <h2 class="text-xl font-semibold text-white mb-4">Доступ запрещён</h2>

                <p class="text-slate-400 mb-8">
                    У вас нет прав для просмотра этой страницы.
                    <br>
                    Возможно, вы пытаетесь зайти в раздел, предназначенный для другой роли.
                </p>

                <Link
                    :href="route('dashboard')"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                >
                    ← Вернуться на дашборд
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
