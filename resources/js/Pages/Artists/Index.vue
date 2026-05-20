<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()

const props = defineProps({
    artists: Array,
    pendingArtists: Array,
    label: Object,
})

const activeTab = ref('approved')

const invite = (artistId) => {
    router.post(route('artists.invite', artistId), {}, { preserveScroll: true })
}

const detach = (artistId) => {
    if (!confirm('Отвязать артиста от лейбла? Все приглашения будут аннулированы.')) return
    router.delete(route('artists.destroy', artistId), { preserveScroll: true })
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 md:p-10 max-w-7xl mx-auto">
            <h1 class="text-[32px] font-bold text-white mb-6">Мои артисты</h1>

            <!-- Вкладки -->
            <div class="flex gap-2 mb-8">
                <button
                    @click="activeTab = 'approved'"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium transition"
                    :class="activeTab === 'approved'
                        ? 'text-white bg-gradient-to-r from-violet-500 to-blue-500'
                        : 'text-slate-400 bg-[#1A1F2B] border border-slate-700 hover:text-white'"
                >
                    Мои артисты ({{ artists.length }})
                </button>
                <button
                    @click="activeTab = 'pending'"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium transition"
                    :class="activeTab === 'pending'
                        ? 'text-white bg-gradient-to-r from-violet-500 to-blue-500'
                        : 'text-slate-400 bg-[#1A1F2B] border border-slate-700 hover:text-white'"
                >
                    Свободные артисты ({{ pendingArtists.length }})
                </button>
            </div>

            <!-- Мои артисты -->
            <div v-if="activeTab === 'approved'">
                <div v-if="artists.length === 0" class="text-slate-400 py-10 text-center">
                    Нет привязанных артистов.
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div
                        v-for="artist in artists"
                        :key="artist.id"
                        class="group relative bg-[#1A1F2B] rounded-[20px] p-6 transition-all duration-300 hover:shadow-[0_0_0_1.5px_rgba(124,58,237,0.5)]"
                    >
                        <div class="flex justify-center mb-4">
                            <div class="w-28 h-28 rounded-2xl p-1"
                                 style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                <div class="w-full h-full rounded-xl flex items-center justify-center text-4xl font-bold text-white"
                                     style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                    {{ artist.stage_name?.charAt(0) ?? 'A' }}
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <h3 class="text-lg font-bold text-white mb-1">{{ artist.stage_name }}</h3>
                            <p class="text-sm text-slate-400 mb-3">{{ artist.real_name }}</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-violet-500/10 text-violet-400">
                                Активен
                            </span>
                        </div>

                        <div class="grid grid-cols-2 divide-x divide-white/5 border-t border-white/5 pt-4 mt-4 mb-4">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-xs text-slate-400">Email</span>
                                <span class="text-sm font-medium text-white truncate max-w-[120px]">
                                    {{ artist.user?.email ?? '—' }}
                                </span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-xs text-slate-400">Треков</span>
                                <span class="text-xl font-bold text-white">{{ artist.songs?.length ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <Link
                                :href="route('artists.show', artist.id)"
                                class="flex-1 py-2.5 rounded-xl text-center text-white text-sm font-medium transition hover:opacity-90"
                                style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                            >
                                Подробнее
                            </Link>
                            <button
                                @click="detach(artist.id)"
                                class="px-4 py-2.5 rounded-xl text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 transition"
                            >
                                Отвязать
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Свободные артисты -->
            <div v-if="activeTab === 'pending'">
                <div v-if="pendingArtists.length === 0" class="text-slate-400 py-10 text-center">
                    Нет свободных артистов.
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div
                        v-for="artist in pendingArtists"
                        :key="artist.id"
                        class="group relative bg-[#1A1F2B] rounded-[20px] p-6 transition-all duration-300 hover:shadow-[0_0_0_1.5px_rgba(124,58,237,0.5)]"
                    >
                        <div class="flex justify-center mb-4">
                            <div class="w-28 h-28 rounded-2xl p-1"
                                 style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                <div class="w-full h-full rounded-xl flex items-center justify-center text-4xl font-bold text-white"
                                     style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                    {{ artist.stage_name?.charAt(0) ?? 'A' }}
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <h3 class="text-lg font-bold text-white mb-1">{{ artist.stage_name }}</h3>
                            <p class="text-sm text-slate-400 mb-3">{{ artist.real_name }}</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400">
                                Ожидает приглашения
                            </span>
                        </div>

                        <div class="grid grid-cols-1 border-t border-white/5 pt-4 mt-4 mb-4">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-xs text-slate-400">Email</span>
                                <span class="text-sm font-medium text-white truncate max-w-[200px]">
                                    {{ artist.user?.email ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <button
                            @click="invite(artist.id)"
                            class="w-full py-2.5 rounded-xl text-center text-white text-sm font-medium transition hover:opacity-90"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                        >
                            Пригласить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
