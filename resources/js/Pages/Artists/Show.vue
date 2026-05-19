<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  artist: Object,
})
</script>

<template>
  <Head title="Карточка артиста" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-3xl p-6">
      <div class="rounded-xl border p-6" style="background-color: #0F1117; border-color: #1e2330;">
        <div class="mb-6 flex items-start justify-between">
          <div>
            <h1 class="text-2xl font-bold text-white">{{ artist.stage_name }}</h1>
            <p class="mt-1 text-sm" style="color: #64748B;">{{ artist.real_name }}</p>
          </div>
          <div class="flex gap-2">
            <Link
              :href="route('artists.edit', artist.id)"
              class="rounded-lg px-3 py-1.5 text-xs font-medium text-white"
              style="background-color: #1A1F2B;"
            >
              Изменить
            </Link>
          </div>
        </div>

        <div class="space-y-3 text-sm">
          <div class="flex justify-between border-b py-2" style="border-color: #1e2330;">
            <span style="color: #94A3B8;">Email</span>
            <span class="text-white">{{ artist.user?.email ?? '—' }}</span>
          </div>
          <div class="flex justify-between border-b py-2" style="border-color: #1e2330;">
            <span style="color: #94A3B8;">О себе</span>
            <span class="text-white">{{ artist.bio ?? 'Нет описания' }}</span>
          </div>
          <div class="flex justify-between py-2">
            <span style="color: #94A3B8;">Треков в системе</span>
            <span class="text-white">{{ artist.songs?.length ?? 0 }}</span>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>