<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex items-center justify-center min-h-[80vh]">
            <div class="text-center max-w-md">
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 rounded-full bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">404</h1>
                <h2 class="text-xl font-semibold text-white mb-4">Страница не найдена</h2>
                <p class="text-slate-400 mb-8">Такой страницы не существует или она была удалена.</p>
                <Link :href="route('dashboard')"
                      class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                      style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                    ← Вернуться на дашборд
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
