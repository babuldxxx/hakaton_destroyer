<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    message: String,
    hasInvitations: Boolean,
})
</script>

<template>
    <div class="flex min-h-screen items-center justify-center px-4 bg-[#0B0E14]">
        <div class="max-w-md text-center">
            <div class="mb-6 flex justify-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full"
                     style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Ожидание подтверждения</h1>
            <p class="text-gray-400">{{ message }}</p>

            <div v-if="hasInvitations" class="mt-6">
                <Link :href="route('artists.invitations')"
                      class="inline-block rounded-lg bg-violet-600 px-6 py-3 text-sm font-medium text-white hover:bg-violet-700">
                    Просмотреть приглашения
                </Link>
            </div>
            <div v-else class="mt-6 text-gray-500 text-sm">
                Пока нет активных приглашений.
            </div>
        </div>
    </div>
</template>
