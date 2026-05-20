<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import LineChart from '@/Components/Charts/LineChart.vue'  // если есть

const props = defineProps({
artist: Object,
revenueData: Object,        // новый пропс
platformRevenue: Array,     // новый пропс
})

const activeTab = ref('overview')
const tabs = [
{ key: 'overview', label: 'Обзор' },
{ key: 'tracks', label: 'Треки' },
{ key: 'finances', label: 'Доходы' },
]
</script>

<template>
    <Head :title="artist.stage_name" />

    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-5xl">

                <!-- Назад -->
                <Link :href="route('artists.index')" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition mb-6">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Назад к артистам
                </Link>

                <!-- Профиль -->
                <div class="bg-[#1A1F2B] rounded-[20px] p-6 md:p-8 mb-8">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-2xl p-1 shrink-0"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                            <div class="w-full h-full rounded-xl bg-[#0B0E14] flex items-center justify-center text-5xl font-bold text-white"
                                style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                {{ artist.stage_name?.charAt(0) ?? 'A' }}
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ artist.stage_name }}</h1>
                            <p class="text-slate-400 mb-4">{{ artist.bio ?? 'Нет описания' }}</p>
                            <p class="text-slate-400 mb-4">{{ artist.real_name }}</p>
                            <p class="text-slate-400 mb-6">{{ artist.user?.email ?? '—' }}</p>

                            <div class="flex gap-8">
                                <div>
                                    <div class="text-sm text-slate-400 mb-1">Треков</div>
                                    <div class="text-3xl font-bold text-white">{{ artist.songs?.length ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вкладки -->
                <div class="inline-flex bg-[#1A1F2B] rounded-full p-1 mb-8">
                    <button
                        v-for="t in tabs"
                        :key="t.key"
                        @click="activeTab = t.key"
                        class="px-5 py-2 rounded-full text-sm font-medium transition"
                        :class="activeTab === t.key ? 'text-white' : 'text-slate-400 hover:text-white'"
                        :style="activeTab === t.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
                    >
                        {{ t.label }}
                    </button>
                </div>

                <!-- Обзор -->
                <div v-if="activeTab === 'overview'" class="space-y-6">
                    <div class="bg-[#1A1F2B] rounded-[20px] p-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Доходы по месяцам</h2>
                        <div class="h-[320px] w-full" v-if="revenueData">
                            <LineChart :chart-data="revenueData" />
                        </div>
                        <div v-else class="text-slate-400 text-center py-10">Нет данных</div>
                    </div>
                </div>

                <!-- Треки -->
                <div v-if="activeTab === 'tracks'" class="bg-[#1A1F2B] rounded-[20px] p-6">
                    <h2 class="text-lg font-semibold text-white mb-6">Треки артиста</h2>
                    <div v-if="!artist.songs?.length" class="text-slate-400 text-center py-10">
                        Нет треков.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Название</th>
                                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Дата релиза</th>
                                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Жанр</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="song in artist.songs" :key="song.id" class="border-b border-white/5 last:border-0">
                                    <td class="py-4 text-sm text-white pr-4">{{ song.title }}</td>
                                    <td class="py-4 text-sm text-slate-400 pr-4">{{ song.released_at ?? '—' }}</td>
                                    <td class="py-4 text-sm text-slate-400 pr-4">{{ song.genre?.name ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Доходы -->
                <div v-if="activeTab === 'finances'" class="bg-[#1A1F2B] rounded-[20px] p-6">
                    <h2 class="text-lg font-semibold text-white mb-6">Доходы по площадкам</h2>
                    <div v-if="platformRevenue?.length" class="space-y-2">
                        <div v-for="(item, i) in platformRevenue" :key="i"
                             class="flex items-center justify-between p-3 rounded bg-gray-800/50">
                            <span class="text-sm text-gray-300">{{ item.platform }}</span>
                            <span class="text-sm font-medium text-white">{{ Number(item.total).toLocaleString('ru-RU') }} ₽</span>
                        </div>
                    </div>
                    <div v-else class="text-slate-400 text-center py-10">
                        Данные о доходах будут доступны позже.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
