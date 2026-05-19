<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  stage_name: '',
  password: '',
})

function submit() {
  form.post(route('artists.store'))
}
</script>

<template>
  <Head title="Добавить артиста" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-lg p-6">
      <h1 class="mb-6 text-2xl font-bold text-white">Добавить артиста</h1>

      <form @submit.prevent="submit" class="space-y-4 rounded-xl border p-6" style="background-color: #0F1117; border-color: #1e2330;">
        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">Имя пользователя</label>
          <input v-model="form.name" type="text" required class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">Email</label>
          <input v-model="form.email" type="email" required class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">Сценическое имя</label>
          <input v-model="form.stage_name" type="text" required class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div>
          <label class="mb-1 block text-xs font-medium" style="color: #94A3B8;">Пароль</label>
          <input v-model="form.password" type="password" required class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm text-white outline-none focus:border-purple-500" style="border-color: #1e2330;" />
        </div>

        <div v-if="form.errors" class="space-y-1">
          <p v-for="err in form.errors" :key="err" class="text-xs text-red-400">{{ err }}</p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full rounded-lg py-2.5 text-sm font-bold text-white transition-opacity hover:opacity-90 disabled:opacity-50"
          style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
        >
          Создать артиста
        </button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>