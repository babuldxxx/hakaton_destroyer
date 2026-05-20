<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    invitations: Array,
})

const acceptForm = useForm({})
const declineForm = useForm({})

const accept = (id) => acceptForm.post(route('artists.invitations.accept', id))
const decline = (id) => declineForm.post(route('artists.invitations.decline', id))
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-white mb-6">Приглашения</h1>

            <div v-if="invitations.length === 0" class="text-gray-400">Нет приглашений.</div>

            <div v-else class="space-y-4">
                <div v-for="inv in invitations" :key="inv.id"
                     class="p-4 rounded-xl bg-[#1A1F2B] border border-[#2D3748] flex justify-between items-center">
                    <div>
                        <p class="text-white font-medium">{{ inv.label?.name ?? 'Лейбл' }}</p>
                        <p class="text-sm text-gray-400">Приглашение от лейбла</p>
                        <p class="text-xs text-gray-500">Статус: {{ inv.status }}</p>
                    </div>

                    <div v-if="inv.status === 'pending'" class="flex gap-2">
                        <button @click="accept(inv.id)"
                                class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-500">
                            Принять
                        </button>
                        <button @click="decline(inv.id)"
                                class="px-4 py-1.5 bg-red-600/80 text-white rounded-lg text-sm hover:bg-red-500">
                            Отклонить
                        </button>
                    </div>
                    <div v-else class="text-sm text-gray-500">
                        {{ inv.status === 'accepted' ? 'Принято' : 'Отклонено' }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
