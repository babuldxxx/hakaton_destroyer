<script setup>
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    invitations: Array,
})

const accept = (id) => {
    router.post(route('artists.invitations.accept', id))
}

const decline = (id) => {
    if (!confirm('Отклонить приглашение?')) return
    router.post(route('artists.invitations.decline', id))
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 md:p-10 max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-white mb-6">Приглашения</h1>

            <div v-if="invitations.length === 0" class="text-slate-400 text-center py-10">
                Нет активных приглашений.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="inv in invitations"
                    :key="inv.id"
                    class="bg-[#1A1F2B] rounded-xl p-6 border border-white/5"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-white font-semibold text-lg">{{ inv.label?.name ?? 'Лейбл' }}</p>
                            <p class="text-slate-400 text-sm">Приглашает вас в свой лейбл</p>
                        </div>
                        <span
                            v-if="inv.status === 'pending'"
                            class="px-3 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400"
                        >
                            Ожидает ответа
                        </span>
                        <span
                            v-else-if="inv.status === 'accepted'"
                            class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400"
                        >
                            Принято
                        </span>
                        <span
                            v-else
                            class="px-3 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400"
                        >
                            Отклонено
                        </span>
                    </div>

                    <div v-if="inv.status === 'pending'" class="flex gap-3">
                        <button
                            @click="accept(inv.id)"
                            class="flex-1 py-2.5 rounded-xl text-sm font-medium text-white transition hover:opacity-90"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                        >
                            Принять
                        </button>
                        <button
                            @click="decline(inv.id)"
                            class="px-6 py-2.5 rounded-xl text-sm font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 transition"
                        >
                            Отклонить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
