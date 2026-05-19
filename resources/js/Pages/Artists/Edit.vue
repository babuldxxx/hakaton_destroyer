<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  artist: Object,
})

const form = useForm({
  stage_name: props.artist.stage_name ?? '',
  real_name: props.artist.real_name ?? '',
  bio: props.artist.bio ?? '',
})

function submit() {
  form.put(route('artists.update', props.artist.id))
}
</script>

<template>
  <Head title="Редактировать артиста" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-lg p-6">
      <h1 class="mb-6 text-2xl font-bold text-white">Редактировать артиста</h1>

      <form @submit.prevent="submit" class="space-y-4 rounded-xl border p-6" style="background-color: #0F1117; border-color: #1e2330;">
        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">Сценическое имя</label>
          <input v-model="form.stage_name" type="text" required class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">Настоящее имя</label>
          <input v-model="form.real_name" type="text" class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">О себе</label>
          <textarea v-model="form.bio" rows="3" class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div v-if="form.errors" class="space-y-1">
          <p v-for="err in form.errors" :key="err" class="text-xs text-red-400">{{ err }}</p>
        </div>

        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex-1 rounded-lg py-2.5 text-sm font-bold text-white transition-opacity hover:opacity-90 disabled:opacity-50"
            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
          >
            Сохранить
          </button>
          <Link
            :href="route('artists.index')"
            class="rounded-lg px-4 py-2.5 text-sm font-medium text-white"
            style="background-color: #1A1F2B;"
          >
            Назад
          </Link>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
