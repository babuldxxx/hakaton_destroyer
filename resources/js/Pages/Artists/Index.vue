<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

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

const isLabel = computed(() => {
    const roles = page.props.auth?.user?.roles
    return roles?.includes('label')
})
</script>

<template>
    <div class="p-6 md:p-10 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-6">Управление артистами</h1>

        <div class="flex gap-4 mb-8">
            <button @click="activeTab = 'approved'"
                :class="activeTab === 'approved' ? 'bg-violet-600 text-white' : 'bg-[#1A1F2B] text-gray-300 border border-[#2D3748]'"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition">
                Мои артисты ({{ artists.length }})
            </button>
            <button @click="activeTab = 'pending'"
                :class="activeTab === 'pending' ? 'bg-violet-600 text-white' : 'bg-[#1A1F2B] text-gray-300 border border-[#2D3748]'"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition">
                Свободные артисты ({{ pendingArtists.length }})
            </button>
        </div>

        <!-- Мои артисты -->
        <div v-if="activeTab === 'approved'">
            <div v-if="artists.length === 0" class="text-gray-400 py-10 text-center">Нет привязанных артистов.</div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <div v-for="artist in artists" :key="artist.id"
                    class="rounded-xl border p-4 transition hover:border-indigo-500/50"
                    style="background-color: #1A1F2B; border-color: #2D3748;">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                            {{ artist.stage_name?.charAt(0) ?? 'A' }}
                        </div>
                        <div>
                            <h3 class="text-white font-semibold truncate">{{ artist.stage_name }}</h3>
                            <p class="text-xs text-gray-400">{{ artist.real_name }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-300 truncate mb-2">{{ artist.user?.email }}</div>
                    <div class="flex items-center justify-between mt-4">
                        <Link :href="route('artists.show', artist.id)"
                            class="text-xs px-3 py-1.5 rounded-md bg-gray-700 text-gray-200 hover:bg-gray-600 transition">
                            Профиль
                        </Link>
                        <button @click="detach(artist.id)"
                            class="text-xs px-3 py-1.5 rounded-md bg-red-600/80 text-white hover:bg-red-500 transition">
                            Отвязать
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Свободные артисты -->
        <div v-if="activeTab === 'pending'">
            <div v-if="pendingArtists.length === 0" class="text-gray-400 py-10 text-center">Нет свободных артистов.</div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <div v-for="artist in pendingArtists" :key="artist.id"
                    class="rounded-xl border p-4 transition hover:border-indigo-500/50"
                    style="background-color: #1A1F2B; border-color: #2D3748;">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                            {{ artist.stage_name?.charAt(0) ?? 'A' }}
                        </div>
                        <div>
                            <h3 class="text-white font-semibold truncate">{{ artist.stage_name }}</h3>
                            <p class="text-xs text-gray-400">{{ artist.real_name }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-300 truncate mb-2">{{ artist.user?.email }}</div>
                    <div class="mt-4">
                        <button @click="invite(artist.id)"
                            class="w-full text-xs px-3 py-1.5 rounded-md bg-violet-600 text-white hover:bg-violet-500 transition">
                            Пригласить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>